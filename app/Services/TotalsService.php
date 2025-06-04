<?php

namespace App\Services;

use App\Data\Totals;
use App\Models\Cart;
use App\Models\Order;

readonly class TotalsService
{
    public function __construct(
        private PriceService $priceService,
        private CartService  $cartService
    )
    {
    }

    public function getCartTotals(Cart $cart): Totals
    {
        $subtotal = $cart->subtotal();
        $deliveryPrice = $this->priceService->getCartShippingPrice($cart);
        $grandTotalVal = $subtotal + ($deliveryPrice?->regular ?? 0);
        $pointsUsed = 0;
        if ($cart->apply_balance && $this->cartService->canApplyBalance($cart)) {
            $balance = \Auth::user()->getBalance();
            // Если баланс больше нуля, применяем его к общей сумме
            if ($balance->value > 0) {
                // Определяем, сколько баллов можно использовать (не больше, чем grand total)
                $pointsUsed = min($balance->value, $grandTotalVal);
                // Вычитаем использованные баллы из общей суммы
                $grandTotalVal -= $pointsUsed;
            }
        }
        $grandTotal = $this->priceService->getPrice($grandTotalVal);
        return new Totals($subtotal, $grandTotal, $pointsUsed);
    }

    public function getOrderTotals(Order $order): Totals
    {
        $subtotal = $order->subtotal();
        $deliveryPrice = $this->priceService->getOrderShippingPrice($order);
        $grandTotalVal = $subtotal + ($deliveryPrice?->regular ?? 0);
        $pointsUsed = 0;
        if ($order->apply_balance) {
            $balance = \Auth::user()->getBalance();
            // Если баланс больше нуля, применяем его к общей сумме
            if ($balance->value > 0) {
                // Определяем, сколько баллов можно использовать (не больше, чем grand total)
                $pointsUsed = min($balance->value, $grandTotalVal);
                // Вычитаем использованные баллы из общей суммы
                $grandTotalVal -= $pointsUsed;
            }
        }
        $grandTotal = $this->priceService->getPrice($grandTotalVal);
        return new Totals($subtotal, $grandTotal, $pointsUsed, $deliveryPrice);
    }
}
