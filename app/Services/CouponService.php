<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CouponService
{
    const USER_COUPON_PERCENTAGE = 5;

    public static function generateUniqueCode(): string
    {
        do {
            $code = Str::random(4) . '-' . Str::random(4);
        } while (Coupon::where('code', $code)->exists());
        return $code;
    }

    public function canApplyCoupon(Coupon $coupon): bool
    {
        return !Auth::user()
            ?->coupon()
            ->where('code', '=', $coupon->code)
            ->exists();
    }
}
