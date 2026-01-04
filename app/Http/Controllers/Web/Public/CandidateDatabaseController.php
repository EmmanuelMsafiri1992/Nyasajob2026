<?php

namespace App\Http\Controllers\Web\Public;

use App\Models\ResumeCredit;
use App\Models\ResumePackage;
use App\Models\ResumeView;
use App\Models\WorkerProfile;
use App\Models\WorkerSkill;
use App\Models\City;
use App\Models\Coupon;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class CandidateDatabaseController extends FrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth')->except(['index', 'show', 'packages']);
    }

    /**
     * Browse candidate database
     */
    public function index(Request $request)
    {
        $countryCode = config('country.code');

        // Build query
        $query = WorkerProfile::with(['user', 'city', 'skills'])
            ->public()
            ->inCountry($countryCode);

        // Filters
        if ($request->filled('skill')) {
            $skillId = $request->input('skill');
            $query->whereHas('skills', function ($q) use ($skillId) {
                $q->where('worker_skills.id', $skillId);
            });
        }

        if ($request->filled('city')) {
            $query->where('city_id', $request->input('city'));
        }

        if ($request->filled('availability')) {
            $query->where('availability_status', $request->input('availability'));
        }

        if ($request->filled('experience_min')) {
            $query->where('experience_years', '>=', $request->input('experience_min'));
        }

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('bio', 'like', "%{$search}%")
                    ->orWhere('custom_skills', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->input('sort', 'recent');
        switch ($sortBy) {
            case 'experience':
                $query->orderByDesc('experience_years');
                break;
            case 'featured':
                $query->orderByDesc('featured_at');
                break;
            default:
                $query->orderByDesc('created_at');
        }

        $candidates = $query->paginate(20);

        // Get filter options
        $skills = WorkerSkill::orderBy('name')->get();
        $cities = City::where('country_code', $countryCode)
            ->whereHas('workerProfiles')
            ->orderBy('name')
            ->get();

        // Check if user has credits
        $userCredits = 0;
        $unlockedIds = [];
        if (auth()->check()) {
            $userCredits = ResumeCredit::getActiveCreditsForUser(auth()->id());
            $unlockedIds = ResumeView::forEmployer(auth()->id())
                ->unlocked()
                ->pluck('worker_profile_id')
                ->toArray();
        }

        MetaTag::set('title', 'Candidate Database - Find Talent | ' . config('app.name'));
        MetaTag::set('description', 'Browse our database of qualified candidates. Search by skills, location, and experience to find the perfect hire.');

        return view('candidates.index', [
            'candidates' => $candidates,
            'skills' => $skills,
            'cities' => $cities,
            'userCredits' => $userCredits,
            'unlockedIds' => $unlockedIds,
            'filters' => $request->only(['skill', 'city', 'availability', 'experience_min', 'q', 'sort']),
        ]);
    }

    /**
     * View candidate profile
     */
    public function show($id)
    {
        $candidate = WorkerProfile::with(['user', 'city', 'skills', 'country'])
            ->public()
            ->findOrFail($id);

        $candidate->incrementViews();

        // Record view if logged in
        $isUnlocked = false;
        $userCredits = 0;
        if (auth()->check()) {
            ResumeView::recordView(auth()->id(), $candidate->id);
            $isUnlocked = ResumeView::hasUnlocked(auth()->id(), $candidate->id);
            $userCredits = ResumeCredit::getActiveCreditsForUser(auth()->id());
        }

        // Related candidates (same skills)
        $relatedCandidates = WorkerProfile::with(['city', 'skills'])
            ->public()
            ->where('id', '!=', $candidate->id)
            ->inCountry($candidate->country_code)
            ->whereHas('skills', function ($q) use ($candidate) {
                $q->whereIn('worker_skills.id', $candidate->skills->pluck('id'));
            })
            ->limit(4)
            ->get();

        MetaTag::set('title', $candidate->title . ' - Candidate Profile | ' . config('app.name'));

        return view('candidates.show', [
            'candidate' => $candidate,
            'isUnlocked' => $isUnlocked,
            'userCredits' => $userCredits,
            'relatedCandidates' => $relatedCandidates,
        ]);
    }

    /**
     * Unlock candidate contact (AJAX)
     */
    public function unlock(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to unlock candidate contacts.',
                'redirect' => route('login'),
            ], 401);
        }

        $candidate = WorkerProfile::findOrFail($id);

        // Check if already unlocked
        if (ResumeView::hasUnlocked(auth()->id(), $candidate->id)) {
            $contactDetails = $candidate->getContactDetailsFor(auth()->user());
            return response()->json([
                'success' => true,
                'already_unlocked' => true,
                'contact' => $contactDetails,
            ]);
        }

        // Try to unlock (uses a credit)
        $view = ResumeView::unlockContact(auth()->id(), $candidate->id);

        if (!$view) {
            return response()->json([
                'success' => false,
                'message' => 'You have no credits remaining. Please purchase a package.',
                'redirect' => route('candidates.packages'),
            ], 402);
        }

        $contactDetails = $candidate->getContactDetailsFor(auth()->user());

        return response()->json([
            'success' => true,
            'message' => 'Contact unlocked successfully!',
            'contact' => [
                'phone' => $candidate->phone,
                'email' => $candidate->email,
                'whatsapp' => $candidate->whatsapp,
                'can_view' => true,
            ],
            'credits_remaining' => ResumeCredit::getActiveCreditsForUser(auth()->id()),
        ]);
    }

    /**
     * Show packages page
     */
    public function packages()
    {
        $packages = ResumePackage::active()->ordered()->get();
        $userCredits = 0;

        if (auth()->check()) {
            $userCredits = ResumeCredit::getActiveCreditsForUser(auth()->id());
        }

        MetaTag::set('title', 'Recruiter Packages - Access Candidate Database | ' . config('app.name'));
        MetaTag::set('description', 'Purchase credits to unlock candidate contact details and build your talent pipeline.');

        return view('candidates.packages', [
            'packages' => $packages,
            'userCredits' => $userCredits,
        ]);
    }

    /**
     * Purchase package
     */
    public function purchase(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:resume_packages,id',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        $package = ResumePackage::active()->findOrFail($request->package_id);
        $user = auth()->user();

        $amount = $package->price;
        $discount = 0;
        $coupon = null;

        // Apply coupon if provided
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::findValidByCode(
                $request->coupon_code,
                $user->id,
                Coupon::APPLICABLE_RESUME_PACKAGES,
                $amount
            );

            if ($coupon) {
                $discount = $coupon->calculateDiscount($amount);
                $amount -= $discount;
            }
        }

        // Free package or 100% discount
        if ($amount <= 0) {
            $credit = $this->createResumeCredit($user, $package, 0, 'free');

            if ($coupon) {
                $coupon->recordUsage($user->id, $package->price, $discount, null, 'resume_package', $package->id);
            }

            flash('Package activated successfully! You have ' . $package->credits . ' credits.')->success();
            return redirect()->route('candidates.index');
        }

        // Create PayPal order
        $paypalService = new PayPalService();
        $returnUrl = route('candidates.purchase.success', ['package' => $package->id, 'coupon' => $coupon?->code]);
        $cancelUrl = route('candidates.packages');

        $order = $paypalService->createOrder($amount, $package->currency_code, $returnUrl, $cancelUrl, [
            'name' => $package->name,
            'description' => $package->credits . ' candidate contact credits',
        ]);

        if (!$order) {
            flash('Failed to create payment. Please try again.')->error();
            return redirect()->back();
        }

        // Find approval URL
        $approvalUrl = null;
        foreach (data_get($order, 'links', []) as $link) {
            if (data_get($link, 'rel') === 'approve') {
                $approvalUrl = data_get($link, 'href');
                break;
            }
        }

        if (!$approvalUrl) {
            flash('Failed to get payment URL. Please try again.')->error();
            return redirect()->back();
        }

        // Store order info in session
        session([
            'resume_package_order' => [
                'order_id' => data_get($order, 'id'),
                'package_id' => $package->id,
                'coupon_code' => $coupon?->code,
                'discount' => $discount,
            ],
        ]);

        return redirect($approvalUrl);
    }

    /**
     * Handle successful payment
     */
    public function purchaseSuccess(Request $request, $package)
    {
        $package = ResumePackage::findOrFail($package);
        $user = auth()->user();
        $orderData = session('resume_package_order');

        if (!$orderData) {
            flash('Invalid payment session.')->error();
            return redirect()->route('candidates.packages');
        }

        // Capture payment
        $paypalService = new PayPalService();
        $orderId = $request->input('token', $orderData['order_id']);
        $captureData = $paypalService->captureOrder($orderId);

        if (!$captureData || data_get($captureData, 'status') !== 'COMPLETED') {
            flash('Payment could not be completed. Please try again.')->error();
            return redirect()->route('candidates.packages');
        }

        // Calculate final amount
        $discount = $orderData['discount'] ?? 0;
        $amount = $package->price - $discount;

        // Create credits
        $transactionId = data_get($captureData, 'id');
        $credit = $this->createResumeCredit($user, $package, $amount, 'paypal', $transactionId);

        // Record coupon usage
        if (!empty($orderData['coupon_code'])) {
            $coupon = Coupon::byCode($orderData['coupon_code'])->first();
            if ($coupon) {
                $coupon->recordUsage($user->id, $package->price, $discount, null, 'resume_package', $package->id);
            }
        }

        // Clear session
        session()->forget('resume_package_order');

        flash('Payment successful! You now have ' . $credit->credits_remaining . ' credits.')->success();
        return redirect()->route('candidates.index');
    }

    /**
     * Create resume credit record
     */
    protected function createResumeCredit($user, $package, $amount, $paymentMethod, $transactionId = null): ResumeCredit
    {
        return ResumeCredit::create([
            'user_id' => $user->id,
            'resume_package_id' => $package->id,
            'credits_purchased' => $package->credits,
            'credits_used' => 0,
            'credits_remaining' => $package->credits,
            'transaction_id' => $transactionId,
            'payment_method' => $paymentMethod,
            'amount_paid' => $amount,
            'currency_code' => $package->currency_code,
            'expires_at' => now()->addDays($package->validity_days),
        ]);
    }

    /**
     * My unlocked candidates
     */
    public function myUnlocked()
    {
        $unlocked = ResumeView::getUnlockedProfiles(auth()->id());
        $userCredits = ResumeCredit::getActiveCreditsForUser(auth()->id());

        return view('candidates.my-unlocked', [
            'unlocked' => $unlocked,
            'userCredits' => $userCredits,
        ]);
    }

    /**
     * My credits history
     */
    public function myCredits()
    {
        $credits = ResumeCredit::forUser(auth()->id())
            ->with('resumePackage')
            ->orderByDesc('created_at')
            ->paginate(20);

        $totalCredits = ResumeCredit::getActiveCreditsForUser(auth()->id());

        return view('candidates.my-credits', [
            'credits' => $credits,
            'totalCredits' => $totalCredits,
        ]);
    }
}
