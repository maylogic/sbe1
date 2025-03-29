<?php

namespace App\Services;

use App\Contracts\IHasShipping;
use App\Data\Price;

class PriceService
{
    public array $cache = [];

    public function __construct(
        private readonly CartService         $cartService,
        private readonly FreeShippingService $freeShippingService,
    )
    {
    }

    public function getCartShippingPrice(IHasShipping $cart): ?Price
    {
        $cacheKey = 'shipping-price-' . $cart->id;
        if (!array_key_exists($cacheKey, $this->cache)) {
            $shippingMethod = $cart->getShippingMethod();
            if (!$shippingMethod) {
                return null;
            }
            if ($this->freeShippingService->isCartEligible($cart)) {
                $this->cache[$cacheKey] = new Price(
                    $shippingMethod->price,
                    1,
                    0
                );
            } else {
                $this->cache[$cacheKey] = $this->getPrice($shippingMethod->price);
            }
        }
        return $this->cache[$cacheKey];
    }

    public function getPrice(int $price): Price
    {
        $coupons = $this->cartService->getCurrentCart()->coupons;
        // большие проценты должны применяться последними, чтобы вычитать меньшие суммы
        $discounts = $coupons->pluck('discount_percentage')->sort()->toArray();
        $discountedPrice = $price;
        foreach ($discounts as $discount) {
            $discountedPrice -= $discountedPrice * $discount / 100;
        }
        return new Price(
            $price,
            $discountedPrice !== $price,
            $discountedPrice
        );
    }
}
