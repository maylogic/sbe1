<?php

namespace App\GraphQL\Mutations;

use App\Exception\GraphqlException;
use App\Models\Cart;
use App\Models\Coupon;
use App\Services\CartService;
use App\Services\CouponService;

readonly class RemoveCoupon
{
    public function __construct(
        private CartService   $cartService
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
        if (!$coupon || !$this->cartService->isCouponApplied($coupon)) {
            throw new GraphqlException('Купон не применен', 'coupon');
        }
        $this->cartService->removeCoupon($coupon);

        return $this->cartService->getCurrentCart()->load('coupons');
    }
}
