<?php

namespace App\Http\Controllers\Web\Public\Account;

use App\Http\Controllers\Web\Public\Auth\Traits\VerificationTrait;
use App\Http\Controllers\Web\Public\FrontController;
use App\Models\PremiumSubscription;
use App\Models\JobSeekerPreference;
use App\Models\Category;
use App\Models\PostType;
use App\Services\PremiumSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PremiumSubscriptionController extends FrontController
{
    use VerificationTrait;

    protected PremiumSubscriptionService $subscriptionService;

    public function __construct()
    {
        parent::__construct();

        $this->subscriptionService = new PremiumSubscriptionService();

        $this->middleware('auth');
    }

    /**
     * Show premium subscription page
     */
    public function index()
    {
        $user = auth()->user();
        $subscription = $user->activePremiumSubscription;
        $preferences = JobSeekerPreference::where('user_id', $user->id)->first();
        $subscriptionHistory = PremiumSubscription::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('account.premium.index', [
            'user' => $user,
            'subscription' => $subscription,
            'preferences' => $preferences,
            'subscriptionHistory' => $subscriptionHistory,
            'hasPremium' => $subscription && $subscription->isActive(),
        ]);
    }

    /**
     * Show subscription checkout page
     */
    public function subscribe()
    {
        $user = auth()->user();

        // Check if premium is enabled
        if (!$this->subscriptionService->isEnabled()) {
            flash('Premium subscriptions are not available at the moment.')->warning();
            return redirect()->route('account.premium.index');
        }

        // Check if user already has active subscription
        if ($user->hasPremiumAccess()) {
            flash('You already have an active premium subscription.')->warning();
            return redirect()->route('account.premium.index');
        }

        return view('account.premium.subscribe', [
            'user' => $user,
            'price' => $this->subscriptionService->getPrice(),
            'currency' => $this->subscriptionService->getCurrency(),
        ]);
    }

    /**
     * Process subscription payment (one-time PayPal payment)
     */
    public function processSubscription(Request $request)
    {
        $request->validate([
            'terms_accepted' => 'required|accepted',
        ], [
            'terms_accepted.required' => 'You must accept the terms and conditions.',
            'terms_accepted.accepted' => 'You must accept the terms and conditions.',
        ]);

        $user = auth()->user();

        // Check if user already has active subscription
        if ($user->hasPremiumAccess()) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active premium subscription.',
            ]);
        }

        // Create pending subscription record
        $subscription = PremiumSubscription::create([
            'user_id' => $user->id,
            'plan_type' => PremiumSubscription::PLAN_JOB_SEEKER_PREMIUM,
            'amount' => $this->subscriptionService->getPrice(),
            'currency' => $this->subscriptionService->getCurrency(),
            'status' => PremiumSubscription::STATUS_PENDING,
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
            'terms_accepted_ip' => $request->ip(),
        ]);

        // Create PayPal order (one-time payment)
        $returnUrl = route('account.premium.success', ['subscription' => $subscription->id]);
        $cancelUrl = route('account.premium.cancel', ['subscription' => $subscription->id]);

        $paypalOrder = $this->subscriptionService->createOrder($user, $returnUrl, $cancelUrl);

        if (!$paypalOrder) {
            $subscription->delete();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create PayPal payment. Please try again.',
            ]);
        }

        // Find approval URL (payer-action link)
        $approvalUrl = null;
        foreach (data_get($paypalOrder, 'links', []) as $link) {
            if (data_get($link, 'rel') === 'payer-action') {
                $approvalUrl = data_get($link, 'href');
                break;
            }
        }

        if (!$approvalUrl) {
            $subscription->delete();
            return response()->json([
                'success' => false,
                'message' => 'Failed to get PayPal approval URL.',
            ]);
        }

        // Save PayPal order ID
        $subscription->update([
            'paypal_subscription_id' => data_get($paypalOrder, 'id'),
            'metadata' => $paypalOrder,
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => $approvalUrl,
        ]);
    }

    /**
     * Handle successful payment - capture the order
     */
    public function success(Request $request, PremiumSubscription $subscription)
    {
        $user = auth()->user();

        if ($subscription->user_id !== $user->id) {
            abort(403);
        }

        // Get the order ID (token from PayPal redirect)
        $orderId = $request->input('token', $subscription->paypal_subscription_id);

        if ($orderId) {
            // Capture the payment
            $captureData = $this->subscriptionService->captureOrder($orderId);

            if ($captureData) {
                $activatedSubscription = $this->subscriptionService->activateSubscription($user, $orderId, $captureData);

                if ($activatedSubscription) {
                    flash('Your premium subscription is now active! Enjoy your benefits.')->success();
                    return redirect()->route('account.premium.preferences');
                }
            }
        }

        flash('There was an issue activating your subscription. Please contact support.')->error();
        return redirect()->route('account.premium.index');
    }

    /**
     * Handle cancelled payment
     */
    public function cancel(Request $request, PremiumSubscription $subscription)
    {
        $user = auth()->user();

        if ($subscription->user_id !== $user->id) {
            abort(403);
        }

        // Delete pending subscription
        if ($subscription->status === PremiumSubscription::STATUS_PENDING) {
            $subscription->delete();
        }

        flash('Subscription cancelled. You can try again anytime.')->info();
        return redirect()->route('account.premium.subscribe');
    }

    /**
     * Cancel active subscription
     */
    public function cancelSubscription(Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $subscription = $user->activePremiumSubscription;

        if (!$subscription) {
            flash('No active subscription found.')->warning();
            return redirect()->route('account.premium.index');
        }

        // Cancel in PayPal
        if ($subscription->paypal_subscription_id) {
            $this->subscriptionService->cancelSubscription(
                $subscription->paypal_subscription_id,
                $request->input('reason', 'User requested cancellation')
            );
        }

        // Mark as cancelled locally
        $subscription->cancel($request->input('reason'));

        flash('Your subscription has been cancelled. You will retain access until ' . $subscription->expires_at->format('M d, Y') . '.')->info();
        return redirect()->route('account.premium.index');
    }

    /**
     * Show job seeker preferences form
     */
    public function preferences()
    {
        $user = auth()->user();

        // Check premium access
        if (!$user->hasPremiumAccess()) {
            flash('You need a premium subscription to access job preferences.')->warning();
            return redirect()->route('account.premium.subscribe');
        }

        $preferences = JobSeekerPreference::firstOrCreate(['user_id' => $user->id]);
        $categories = Category::orderBy('name')->get();
        $jobTypes = PostType::orderBy('name')->get();

        return view('account.premium.preferences', [
            'user' => $user,
            'preferences' => $preferences,
            'categories' => $categories,
            'jobTypes' => $jobTypes,
            'urgencyLevels' => JobSeekerPreference::getUrgencyLevels(),
            'experienceLevels' => JobSeekerPreference::getExperienceLevels(),
            'alertFrequencies' => JobSeekerPreference::getAlertFrequencies(),
        ]);
    }

    /**
     * Update job seeker preferences
     */
    public function updatePreferences(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasPremiumAccess()) {
            return response()->json(['success' => false, 'message' => 'Premium subscription required.']);
        }

        $validated = $request->validate([
            'desired_job_title' => 'nullable|string|max:255',
            'job_keywords' => 'nullable|string|max:500',
            'preferred_categories' => 'nullable|array',
            'preferred_job_types' => 'nullable|array',
            'remote_only' => 'boolean',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0',
            'salary_currency' => 'nullable|string|size:3',
            'salary_period' => 'nullable|string|in:hourly,daily,weekly,monthly,yearly',
            'urgency_level' => 'nullable|string|in:not_urgent,within_month,within_week,immediate',
            'available_from' => 'nullable|date',
            'availability_notes' => 'nullable|string|max:500',
            'experience_level' => 'nullable|string|in:entry,junior,mid,senior,executive',
            'years_of_experience' => 'nullable|integer|min:0|max:50',
            'key_skills' => 'nullable|string|max:1000',
            'qualifications' => 'nullable|string|max:2000',
            'languages' => 'nullable|string|max:500',
            'cv_summary' => 'nullable|string|max:2000',
            'career_goals' => 'nullable|string|max:2000',
            'email_alerts' => 'boolean',
            'alert_frequency' => 'nullable|string|in:instant,daily,weekly',
            'max_alerts_per_day' => 'nullable|integer|min:1|max:50',
        ]);

        $preferences = JobSeekerPreference::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Your preferences have been saved.',
                'completion' => $preferences->profileCompletion,
            ]);
        }

        flash('Your job preferences have been saved.')->success();
        return redirect()->route('account.premium.preferences');
    }

    /**
     * Show job matches based on preferences
     */
    public function jobMatches()
    {
        $user = auth()->user();

        if (!$user->hasPremiumAccess()) {
            flash('You need a premium subscription to access job matching.')->warning();
            return redirect()->route('account.premium.subscribe');
        }

        $preferences = JobSeekerPreference::where('user_id', $user->id)->first();

        if (!$preferences) {
            flash('Please set up your job preferences first.')->info();
            return redirect()->route('account.premium.preferences');
        }

        $matches = $preferences->buildJobSearchQuery()
            ->with(['category', 'postType', 'city'])
            ->paginate(20);

        // Increment match count
        $preferences->incrementJobMatches($matches->total());

        return view('account.premium.job-matches', [
            'user' => $user,
            'preferences' => $preferences,
            'matches' => $matches,
        ]);
    }

    /**
     * Show CV refinement tips
     */
    public function cvTips()
    {
        $user = auth()->user();

        if (!$user->hasPremiumAccess()) {
            flash('You need a premium subscription to access CV tips.')->warning();
            return redirect()->route('account.premium.subscribe');
        }

        $preferences = JobSeekerPreference::where('user_id', $user->id)->first();
        $preferences?->incrementCvReviews();

        return view('account.premium.cv-tips', [
            'user' => $user,
            'preferences' => $preferences,
        ]);
    }

    /**
     * Show interview preparation tips
     */
    public function interviewPrep()
    {
        $user = auth()->user();

        if (!$user->hasPremiumAccess()) {
            flash('You need a premium subscription to access interview prep.')->warning();
            return redirect()->route('account.premium.subscribe');
        }

        $preferences = JobSeekerPreference::where('user_id', $user->id)->first();
        $preferences?->incrementInterviewTips();

        return view('account.premium.interview-prep', [
            'user' => $user,
            'preferences' => $preferences,
        ]);
    }

    /**
     * Handle PayPal webhook
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();

        Log::info('PayPal Premium Webhook', ['payload' => $payload]);

        $this->subscriptionService->handleWebhook($payload);

        return response()->json(['status' => 'success']);
    }
}
