<?php

namespace App\Services;

use App\Contracts\IHasPrice;
use App\Data\Price;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductOptionItem;
use App\Models\SalesRule;
use App\Models\ShippingMethod;
use Illuminate\Support\Collection;

class PriceService
{
    public array $productPrices = [];
    public array $cartItemPrices = [];
    public array $orderItemPrices = [];
    public array $shippingMethodPrices = [];

    public function __construct(
        private readonly CartService      $cartService,
        private readonly SalesRuleService $salesRuleService
    )
    {
    }

    public function getProductPrice(Product $product)
    {
        if (!array_key_exists($product->id, $this->productPrices)) {
            $this->productPrices[$product->id] = $this->_getProductPrice($product);
        }

        return $this->productPrices[$product->id];
    }

    public function getCartItemPrice(CartItem $cartItem)
    {
        if (!array_key_exists($cartItem->id, $this->cartItemPrices)) {
            $this->cartItemPrices[$cartItem->id] = $this->_getCartItemPrice($cartItem);
        }

        return $this->cartItemPrices[$cartItem->id];
    }

    public function getOrderItemPrice(OrderItem $orderItem)
    {
        if (!array_key_exists($orderItem->id, $this->orderItemPrices)) {
            $this->orderItemPrices[$orderItem->id] = $this->_getOrderItemPrice($orderItem);
        }

        return $this->orderItemPrices[$orderItem->id];
    }

    public function getShippingMethodPrice(ShippingMethod $shippingMethod)
    {
        if (!array_key_exists($shippingMethod->id, $this->shippingMethodPrices)) {
            $this->shippingMethodPrices[$shippingMethod->id] = $this->_getShippingMethodPrice($shippingMethod);
        }

        return $this->shippingMethodPrices[$shippingMethod->id];
    }

    private function _getProductPrice(Product $product): Price
    {
        $salesRules = $this->salesRuleService->getActiveProductRules();
        return $this->_getPrice($product->price, $salesRules, ['product' => $product]);
    }

    /**
     * @param int $price
     * @param Collection<SalesRule> $salesRules
     * @param array $context
     * @return Price
     */
    private function _getPrice(int $price, Collection $salesRules, array $context = []): Price
    {
        $discountedPrice = $price;

        foreach ($salesRules as $salesRule) {
            if ($this->salesRuleService->evaluate($salesRule, $context)) {
                $discountedPrice = $this->salesRuleService->apply($salesRule, $discountedPrice);
            }
        }

        // применяем купоны
        $coupons = $this->cartService->getCurrentCart()->coupons;
        // большие проценты должны применяться последними, чтобы вычитать меньшие суммы
        $discounts = $coupons->pluck('discount_percentage')->sort()->toArray();
        foreach ($discounts as $discount) {
            $discountedPrice -= $discountedPrice * $discount / 100;
        }

        return Price::create($price, $discountedPrice);
    }

    public function getPrice(?IHasPrice $priceHolder = null): Price
    {
        if ($priceHolder instanceof Product) {
            return $this->getProductPrice($priceHolder);
        }
        if ($priceHolder instanceof CartItem) {
            return $this->getCartItemPrice($priceHolder);
        }
        if ($priceHolder instanceof OrderItem) {
            return $this->getOrderItemPrice($priceHolder);
        }
        if ($priceHolder instanceof ShippingMethod) {
            return $this->getShippingMethodPrice($priceHolder);
        }
        if ($priceHolder === null) {
            return Price::create(0);
        }
        return Price::create($priceHolder->price);
    }

    private function _getCartItemPrice(CartItem $cartItem): Price
    {
        return $this->_getItemPrice($cartItem);
    }

    private function _getOrderItemPrice(OrderItem $orderItem): Price
    {
        return $this->_getItemPrice($orderItem);
    }

    private function _getItemPrice(CartItem|OrderItem $cartOrOrderItem): Price
    {
        $productPrice = $cartOrOrderItem->product->price;
        $sizePrice = $cartOrOrderItem->product_size?->price ?? 0;
        $optionsPrices = $cartOrOrderItem->option_items->reduce(
            fn(int $carry, ProductOptionItem $item) => $carry + $item->price, 0);
        $price = $productPrice + $sizePrice + $optionsPrices;
        // применяем sales rules
        $salesRules = $this->salesRuleService->getActiveProductRules();
        return $this->_getPrice($price, $salesRules, ['product' => $cartOrOrderItem->product]);
    }

    private function _getShippingMethodPrice(ShippingMethod $shippingMethod): Price
    {
        $price = $shippingMethod->price;
        $discountedPrice = $price;
        // применяем sales rules
        $salesRules = $this->salesRuleService->getActiveShippingRules();
        foreach ($salesRules as $salesRule) {
            if ($this->salesRuleService->evaluate($salesRule)) {
                $discountedPrice = $this->salesRuleService->apply($salesRule, $discountedPrice);
            }
        }

        return Price::create($price, $discountedPrice);
    }
}
