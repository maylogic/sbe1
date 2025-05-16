<?php

namespace App\Services\Storefront;

use App\Models\Coupon;
use App\Models\User;
use App\Payload\Storefront\StorefrontCustomerLoginPayload;
use App\Payload\Storefront\StorefrontCustomerRegisterPayload;
use App\Services\CouponService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StorefrontCustomerAuthService
{
    public function register(StorefrontCustomerRegisterPayload $payload)
    {
        return DB::transaction(function () use ($payload) {
            $coupon = Coupon::factory()->create([
                'discount_percentage' => CouponService::USER_COUPON_PERCENTAGE
            ]);
            return User::factory()
                ->hasBalance()
                ->create(
                    [
                        'email' => $payload->email,
                        'password' => Hash::make($payload->password),
                        'name' => $payload->name,
                        'coupon_id' => $coupon->id
                    ]
                );
        });
    }

    public function login(StorefrontCustomerLoginPayload $payload): User
    {
        if (!Auth::attempt(
            [
                'email' => $payload->email,
                'password' => $payload->password
            ]
        )) {
            throw new \InvalidArgumentException('Invalid credentials');
        }

        /** @var User $user */
        $user = Auth::user();
        return $user;
    }
}
