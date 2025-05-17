<?php

namespace App\Services;

use App\Data\ShippingInfo;
use App\Enum\ShippingMethodProvider;
use App\Models\Cart;
use App\Models\ShippingMethod;

readonly class ShippingService
{
    public function __construct(private CartService $cartService)
    {
    }

    public function getCdekPrice(ShippingMethod $shippingMethod, Cart $cart): ShippingInfo
    {
        return new ShippingInfo($shippingMethod->price, 2);
    }

    public function getBoxberryPrice(ShippingMethod $shippingMethod, Cart $cart): ShippingInfo
    {
        return new ShippingInfo($shippingMethod->price, 3);
    }

    public function getPostPrice(ShippingMethod $shippingMethod, Cart $cart): ShippingInfo
    {
        return new ShippingInfo($shippingMethod->price, 5);
    }

    public function getPostFirstClassPrice(ShippingMethod $shippingMethod, Cart $cart): ShippingInfo
    {
        return new ShippingInfo($shippingMethod->price, 4);
    }

    public function getShippingInfo(ShippingMethod $shippingMethod): ShippingInfo
    {
        $cart = $this->cartService->getCurrentCart();
        return match ($shippingMethod->provider) {
            ShippingMethodProvider::CDEK => $this->getCdekPrice($shippingMethod, $cart),
            ShippingMethodProvider::BOXBERRY => $this->getBoxberryPrice($shippingMethod, $cart),
            ShippingMethodProvider::POST => $this->getPostPrice($shippingMethod, $cart),
            ShippingMethodProvider::POST_FIRST_CLASS => $this->getPostFirstClassPrice($shippingMethod, $cart),
            default => new ShippingInfo($shippingMethod->price, 6)
        };
    }
}
