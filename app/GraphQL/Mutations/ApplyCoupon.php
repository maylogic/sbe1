<?php

namespace App\GraphQL\Mutations;

use App\Exception\GraphqlException;
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
        if (!$coupon) {
            throw new GraphqlException('Такого купона нет', 'coupon');
        }
        if ($this->cartService->isCouponApplied($coupon)) {
            throw new GraphqlException('Купон уже применен', 'coupon');
        }
        if (!$this->couponService->canApplyCoupon($coupon)) {
            throw new GraphqlException('Купон не применим', 'coupon');
        }

        try {
            $this->cartService->applyCoupon($coupon);
        } catch (\DomainException | \InvalidArgumentException $e) {
            // "Купон закончился" and other domain errors must reach the storefront
            // as readable messages, not as an opaque HTTP 500.
            throw new GraphqlException($e->getMessage(), 'coupon');
        }

        // The cart instance is memoized inside CartService and its `coupons`
        // relation was already loaded as empty, so it has to be reloaded.
        return $this->cartService->getCurrentCart()->load('coupons');
    }
}
