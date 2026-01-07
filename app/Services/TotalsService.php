<?php

namespace App\Services;

use App\Data\Price;
use App\Data\ShippingPriceContext;
use App\Data\Totals;
use App\Models\Cart;
use App\Models\Order;

readonly class TotalsService
{
    public function __construct(
        private PriceService   $priceService,
        private BalanceService $balanceService
    )
    {
    }

    public function getCartTotals(Cart $cart): Totals
    {
        $subtotal = $this->getCartSubtotal($cart);
        $cart->subtotal = $subtotal; // for sales expressions
        $shippingPrice = $this->priceService->getPrice($cart->shipping_method, new ShippingPriceContext($cart));
        $grandTotalRegular = $subtotal->regular;
        $grandTotalDiscounted = $subtotal->discounted;
        $pointsUsed = 0;
        if ($cart->apply_balance && $this->balanceService->canApplyBalance($cart)) {
            $balance = \Auth::user()->getBalance();
            // Если баланс больше нуля, применяем его к общей сумме
            if ($balance->value > 0) {
                // Определяем, сколько баллов можно использовать (не больше, чем grand total)
                $pointsUsed = min($balance->value, $grandTotalDiscounted);
                // Вычитаем использованные баллы из общей суммы
                $grandTotalDiscounted -= $pointsUsed;
            }
        }

        $realShippingPrice = $shippingPrice?->getRegularOrDiscounted();
        if ($realShippingPrice) {
            $grandTotalRegular += $realShippingPrice;
            $grandTotalDiscounted += $realShippingPrice;
        }
        $grandTotal = Price::create($grandTotalRegular, $grandTotalDiscounted);

        return new Totals($subtotal->regular, $grandTotal, $pointsUsed, $shippingPrice);
    }

    public function getOrderTotals(Order $order): Totals
    {
        $grandTotal = Price::create($order->grand_total_regular, $order->grand_total_discounted);
        $shippingPrice = Price::create($order->shipping_price_regular, $order->shipping_price_discounted);
        return new Totals($order->subtotal, $grandTotal, 0, $shippingPrice);
    }

    public function getCartSubtotal(Cart $cart): Price
    {
        $regular = 0;
        $discounted = 0;
        foreach ($cart->items as $item) {
            $price = $this->priceService->getPrice($item);
            $regular += $item->quantity * $price->regular;
            $discounted += $item->quantity * $price->discounted;
        }
        return Price::create($regular, $discounted);
    }
}
