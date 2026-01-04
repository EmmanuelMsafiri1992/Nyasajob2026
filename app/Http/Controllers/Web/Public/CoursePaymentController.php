<?php

namespace App\Http\Controllers\Web\Public;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Coupon;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class CoursePaymentController extends FrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    /**
     * Show course checkout page
     */
    public function checkout(Request $request, $courseId)
    {
        $course = Course::where('is_published', true)->findOrFail($courseId);
        $user = auth()->user();

        // Check if already enrolled
        if ($course->isEnrolledBy($user->id)) {
            flash('You are already enrolled in this course.')->info();
            return redirect()->route('courses.show', $course->slug);
        }

        // Free course - enroll directly
        if ($course->is_free || $course->price <= 0) {
            return $this->enrollFree($course, $user);
        }

        MetaTag::set('title', 'Checkout - ' . $course->title . ' | ' . config('app.name'));

        return view('courses.checkout', [
            'course' => $course,
        ]);
    }

    /**
     * Process course payment
     */
    public function process(Request $request, $courseId)
    {
        $request->validate([
            'coupon_code' => 'nullable|string|max:50',
        ]);

        $course = Course::where('is_published', true)->findOrFail($courseId);
        $user = auth()->user();

        // Check if already enrolled
        if ($course->isEnrolledBy($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You are already enrolled in this course.',
            ], 400);
        }

        // Free course
        if ($course->is_free || $course->price <= 0) {
            $this->enrollFree($course, $user);
            return response()->json([
                'success' => true,
                'redirect' => route('courses.show', $course->slug),
            ]);
        }

        $amount = $course->price;
        $discount = 0;
        $coupon = null;

        // Apply coupon
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::findValidByCode(
                $request->coupon_code,
                $user->id,
                Coupon::APPLICABLE_COURSES,
                $amount
            );

            if ($coupon && !$coupon->isItemExcluded('course', $course->id)) {
                $discount = $coupon->calculateDiscount($amount);
                $amount -= $discount;
            }
        }

        // 100% discount
        if ($amount <= 0) {
            $enrollment = $this->createEnrollment($course, $user, 0, 'free', null, $coupon, $discount);

            if ($coupon) {
                $coupon->recordUsage($user->id, $course->price, $discount, null, 'course', $course->id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Enrollment successful!',
                'redirect' => route('courses.show', $course->slug),
            ]);
        }

        // Create PayPal order
        $paypalService = new PayPalService();
        $returnUrl = route('courses.payment.success', [
            'course' => $course->id,
            'coupon' => $coupon?->code,
        ]);
        $cancelUrl = route('courses.checkout', $course->id);

        $order = $paypalService->createOrder(
            $amount,
            $course->currency_code ?? 'USD',
            $returnUrl,
            $cancelUrl,
            [
                'name' => $course->title,
                'description' => 'Course enrollment: ' . $course->title,
            ],
            'course_' . $course->id . '_' . $user->id . '_' . time()
        );

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment. Please try again.',
            ], 500);
        }

        $approvalUrl = PayPalService::getApprovalUrl($order);

        if (!$approvalUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment URL.',
            ], 500);
        }

        // Store order info in session
        session([
            'course_payment' => [
                'order_id' => data_get($order, 'id'),
                'course_id' => $course->id,
                'coupon_code' => $coupon?->code,
                'discount' => $discount,
                'original_amount' => $course->price,
                'final_amount' => $amount,
            ],
        ]);

        return response()->json([
            'success' => true,
            'redirect' => $approvalUrl,
        ]);
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = auth()->user();
        $paymentData = session('course_payment');

        if (!$paymentData || $paymentData['course_id'] != $courseId) {
            flash('Invalid payment session.')->error();
            return redirect()->route('courses.show', $course->slug);
        }

        // Check if already enrolled
        if ($course->isEnrolledBy($user->id)) {
            session()->forget('course_payment');
            flash('You are already enrolled in this course.')->info();
            return redirect()->route('courses.show', $course->slug);
        }

        // Capture payment
        $paypalService = new PayPalService();
        $orderId = $request->input('token', $paymentData['order_id']);
        $captureData = $paypalService->captureOrder($orderId);

        if (!$captureData || data_get($captureData, 'status') !== 'COMPLETED') {
            flash('Payment could not be completed. Please try again.')->error();
            return redirect()->route('courses.checkout', $course->id);
        }

        $transactionId = data_get($captureData, 'purchase_units.0.payments.captures.0.id', $orderId);
        $discount = $paymentData['discount'] ?? 0;

        // Get coupon
        $coupon = null;
        if (!empty($paymentData['coupon_code'])) {
            $coupon = Coupon::byCode($paymentData['coupon_code'])->first();
        }

        // Create enrollment
        $enrollment = $this->createEnrollment(
            $course,
            $user,
            $paymentData['final_amount'],
            'paypal',
            $transactionId,
            $coupon,
            $discount
        );

        // Record coupon usage
        if ($coupon) {
            $coupon->recordUsage($user->id, $course->price, $discount, null, 'course', $course->id);
        }

        // Clear session
        session()->forget('course_payment');

        flash('Payment successful! You are now enrolled in ' . $course->title)->success();
        return redirect()->route('courses.show', $course->slug);
    }

    /**
     * Handle cancelled payment
     */
    public function cancel(Request $request, $courseId)
    {
        session()->forget('course_payment');

        flash('Payment was cancelled.')->info();
        return redirect()->route('courses.checkout', $courseId);
    }

    /**
     * Enroll in free course
     */
    protected function enrollFree(Course $course, $user)
    {
        $this->createEnrollment($course, $user, 0, 'free');

        flash('You have been enrolled in ' . $course->title)->success();
        return redirect()->route('courses.show', $course->slug);
    }

    /**
     * Create enrollment record
     */
    protected function createEnrollment(
        Course $course,
        $user,
        float $amount,
        string $paymentMethod,
        ?string $transactionId = null,
        ?Coupon $coupon = null,
        float $discount = 0
    ): CourseEnrollment {
        $enrollment = CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'amount_paid' => $amount,
            'currency_code' => $course->currency_code ?? 'USD',
            'coupon_id' => $coupon?->id,
            'discount_amount' => $discount,
            'payment_status' => $amount <= 0 ? 'free' : 'completed',
            'transaction_id' => $transactionId,
        ]);

        // Increment enrollment count
        $course->increment('enrollment_count');

        return $enrollment;
    }
}
