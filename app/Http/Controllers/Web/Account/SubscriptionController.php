<?php

namespace App\Http\Controllers\Web\Account;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class SubscriptionController extends AccountBaseController
{
    /**
     * Display subscription plans
     */
    public function index()
    {
        $plans = SubscriptionPlan::active()->orderBy('sort_order')->get();
        $currentSubscription = Auth::user()->activeSubscription;

        // Meta Tags
        MetaTag::set('title', t('Subscription Plans'));
        MetaTag::set('description', t('Choose the subscription plan that fits your needs'));

        return appView('account.subscriptions.index', compact('plans', 'currentSubscription'));
    }

    /**
     * Show subscription checkout
     */
    public function checkout($planSlug)
    {
        $plan = SubscriptionPlan::where('slug', $planSlug)->where('is_active', true)->firstOrFail();

        // Meta Tags
        MetaTag::set('title', t('Subscribe to :plan', ['plan' => $plan->name]));
        MetaTag::set('description', t('Complete your subscription to :plan', ['plan' => $plan->name]));

        return appView('account.subscriptions.checkout', compact('plan'));
    }

    /**
     * Process subscription payment
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'payment_method' => 'required|string',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->subscription_plan_id);
        $user = Auth::user();

        // Get PayPal payment method from database
        $paymentMethod = \App\Models\PaymentMethod::where('name', 'paypal')
            ->where('active', 1)
            ->first();

        if (!$paymentMethod) {
            return redirect()->back()
                ->with('error', 'PayPal payment is not available at the moment.');
        }

        // Store subscription data in session for payment callback
        session()->put('subscription_payment', [
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'payment_method_id' => $paymentMethod->id,
            'amount' => $plan->price,
        ]);

        // Redirect to payment confirmation page
        return redirect()->route('subscriptions.payment.confirmation', $plan->id);
    }

    /**
     * Payment confirmation - Shows payment processing page
     */
    public function paymentConfirmation(Request $request, $planId)
    {
        $params = session('subscription_payment');

        if (empty($params)) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'Payment session expired. Please try again.');
        }

        $plan = SubscriptionPlan::findOrFail($planId);
        $paymentMethod = \App\Models\PaymentMethod::find($params['payment_method_id']);

        if (empty($paymentMethod)) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'Payment method not found.');
        }

        // Meta Tags
        MetaTag::set('title', t('Complete Payment'));
        MetaTag::set('description', t('Complete your payment to activate subscription'));

        return appView('account.subscriptions.payment', compact('plan', 'paymentMethod'));
    }

    /**
     * Payment success callback
     */
    public function paymentSuccess($planId)
    {
        $params = session('subscription_payment');

        if (empty($params)) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'Payment session expired.');
        }

        $plan = SubscriptionPlan::findOrFail($planId);
        $user = Auth::user();

        // Cancel any existing active subscription
        $existingSubscription = $user->activeSubscription;
        if ($existingSubscription) {
            $existingSubscription->cancel();
        }

        // Calculate subscription period
        $startsAt = now();
        $endsAt = $this->calculateEndDate($startsAt, $plan->interval, $plan->interval_count);

        // Create new subscription
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'payment_method' => 'paypal',
            'transaction_id' => request()->input('transaction_id', 'TXN-' . strtoupper(uniqid())),
            'amount_paid' => $plan->price,
        ]);

        // Clear session
        session()->forget('subscription_payment');

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription activated successfully! Welcome to ' . $plan->name . ' plan.');
    }

    /**
     * Payment cancel callback
     */
    public function paymentCancel($planId)
    {
        session()->forget('subscription_payment');

        return redirect()->route('subscriptions.index')
            ->with('error', 'Payment was cancelled. Your subscription was not activated.');
    }

    /**
     * Cancel subscription
     */
    public function cancel()
    {
        $subscription = Auth::user()->activeSubscription;

        if (!$subscription) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'No active subscription found.');
        }

        $subscription->cancel();

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription cancelled successfully.');
    }

    /**
     * Calculate subscription end date
     */
    private function calculateEndDate($startDate, $interval, $count)
    {
        switch ($interval) {
            case 'daily':
                return $startDate->copy()->addDays($count);
            case 'weekly':
                return $startDate->copy()->addWeeks($count);
            case 'monthly':
                return $startDate->copy()->addMonths($count);
            case 'yearly':
                return $startDate->copy()->addYears($count);
            default:
                return $startDate->copy()->addMonth();
        }
    }
}
