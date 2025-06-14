<?php

namespace App\Services;

use App\Data\Price;
use App\Data\Totals;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;

readonly class TotalsService
{
    public function __construct(
        private SalesRuleService $salesRuleService,
        private PriceService     $priceService,
        private BalanceService   $balanceService
    )
    {
    }

    public function getCartTotals(Cart $cart): Totals
    {
        $subtotal = $this->getCartSubtotal($cart);
        $cart->subtotal = $subtotal;
        $deliveryPrice = $this->priceService->getPrice($cart->shipping_method);
        $grandTotalRegular = $subtotal;
        $grandTotalDiscounted = $grandTotalRegular;
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

        // применяем shipping sales rules
        $salesRules = $this->salesRuleService->getActiveCartRules();
        foreach ($salesRules as $salesRule) {
            if ($this->salesRuleService->evaluate($salesRule, ['cart' => $cart])) {
                $grandTotalDiscounted = $this->salesRuleService->apply($salesRule, $grandTotalDiscounted);
            }
        }

        $grandTotal = Price::create($grandTotalRegular, $grandTotalDiscounted);

        return new Totals($subtotal, $grandTotal, $pointsUsed, $deliveryPrice);
    }

    public function getOrderTotals(Order $order): Totals
    {
        return new Totals(0, Price::create(0), 0, Price::create(0));
    }

    public function getCartSubtotal(Cart $cart): int
    {
        return $cart->items->reduce(
            fn(int $carry, CartItem $item) => $carry + $item->quantity * $this->priceService->getPrice($item)->regular,
            0
        );
    }
}
