<?php

namespace App\Services;

use App\Contracts\IHasPrice;
use App\Data\Abstract\PriceContext;
use App\Data\Price;
use App\Data\ProductPriceContext;
use App\Data\ShippingPriceContext;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductOptionItem;
use App\Models\SalesRule;
use App\Models\ShippingMethod;
use App\Traits\MemoizeMethods;
use Illuminate\Support\Collection;

class PriceService
{
    use MemoizeMethods;

    public function __construct(
        private readonly CartService      $cartService,
        private readonly SalesRuleService $salesRuleService
    )
    {
    }

    public function getProductPrice(Product $product)
    {
        return $this->memoize("ProductPrice::$product->id", fn() => $this->_getProductPrice($product));
    }

    public function getCartItemPrice(CartItem $cartItem)
    {
        return $this->memoize("CartItemPrice::$cartItem->id", fn() => $this->_getCartItemPrice($cartItem));
    }

    public function getOrderItemPrice(OrderItem $orderItem)
    {
        return $this->memoize("OrderItemPrice::$orderItem->id", fn() => $this->_getOrderItemPrice($orderItem));
    }

    public function getShippingMethodPrice(ShippingMethod $shippingMethod, ShippingPriceContext $context)
    {
        $key = "ShippingMethodPrice::$shippingMethod->id::$context->key";
        return $this->memoize($key,
            fn() => $this->_getShippingMethodPrice($shippingMethod, $context));
    }

    private function _getProductPrice(Product $product): Price
    {
        $salesRules = $this->salesRuleService->getActiveProductRules();
        return $this->_getPrice($product->price, $salesRules, new ProductPriceContext($product));
    }

    /**
     * @param int $price
     * @param Collection<SalesRule> $salesRules
     * @param PriceContext $context
     * @return Price
     */
    private function _getPrice(int $price, Collection $salesRules, PriceContext $context): Price
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

    public function getPrice(?IHasPrice $priceHolder = null, ?ShippingPriceContext $context = null): ?Price
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
            if (!$context) {
                throw new \LogicException('Context is required for shipping method price calculation.');
            }
            return $this->getShippingMethodPrice($priceHolder, $context);
        }
        if ($priceHolder === null) {
            return null;
        }
        return Price::create($priceHolder->getPrice());
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
        return $this->_getPrice($price, $salesRules, new ProductPriceContext($cartOrOrderItem->product));
    }

    private function _getShippingMethodPrice(ShippingMethod $shippingMethod, ShippingPriceContext $context): Price
    {
        $price = $shippingMethod->price;
        $discountedPrice = $price;
        // применяем sales rules
        $salesRules = $this->salesRuleService->getActiveShippingRules();
        foreach ($salesRules as $salesRule) {
            if ($this->salesRuleService->evaluate($salesRule, $context)) {
                $discountedPrice = $this->salesRuleService->apply($salesRule, $discountedPrice);
            }
        }

        return Price::create($price, $discountedPrice);
    }
}
