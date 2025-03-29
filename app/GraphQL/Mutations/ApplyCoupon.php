<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Models\Coupon;
use App\Services\CartService;
use App\Services\CouponService;

readonly class ApplyCoupon
{
    public function __construct(
        private CartService   $cartService,
        private CouponService $couponService
    )
    {
    }

    public function __invoke(
        $rootValue,
        array $args
    ): Cart
    {
        $code = $args['code'];
        $coupon = Coupon::whereCode($code)->first();
        if ($this->cartService->isCouponApplied($coupon)) {
            throw new \InvalidArgumentException('Купон уже применен');
        }
        if (!$this->couponService->canApplyCoupon($coupon)) {
            throw new \InvalidArgumentException('Купон не применим');
        }
        $this->cartService->applyCoupon($coupon);

        return $this->cartService->getCurrentCart();
    }
}
