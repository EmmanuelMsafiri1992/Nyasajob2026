<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Validate a coupon code
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'type' => 'nullable|string|in:all,packages,courses,subscriptions,resume_packages',
        ]);

        $userId = auth()->id() ?? 0;
        $type = $request->input('type', 'all');
        $amount = (float) $request->input('amount');

        $coupon = Coupon::findValidByCode($request->code, $userId, $type, $amount);

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid or expired coupon code.',
            ], 422);
        }

        $discount = $coupon->calculateDiscount($amount);

        return response()->json([
            'valid' => true,
            'coupon' => [
                'code' => $coupon->code,
                'name' => $coupon->name,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
                'discount_text' => $coupon->discount_text,
            ],
            'original_amount' => $amount,
            'discount_amount' => $discount,
            'final_amount' => $amount - $discount,
            'message' => "Coupon applied! You save {$coupon->discount_text}",
        ]);
    }

    /**
     * Remove coupon (just returns original pricing)
     */
    public function remove(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        return response()->json([
            'valid' => false,
            'original_amount' => (float) $request->input('amount'),
            'discount_amount' => 0,
            'final_amount' => (float) $request->input('amount'),
            'message' => 'Coupon removed.',
        ]);
    }
}
