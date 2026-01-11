<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;

class BalanceService
{
    public function canApplyBalance(Cart|Order $cartOrOrder, ?User $user = null): bool
    {
        if ($cartOrOrder instanceof Order) {
            return true;
        }
        if ($user) {
            return $cartOrOrder->user_id === $user->id
                && $user->getBalance()->value > 0;
        }
        return false;
    }
}
