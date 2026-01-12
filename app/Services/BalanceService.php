<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\User;

class BalanceService
{
    public function canApplyBalance(Cart $cart, ?User $user = null): bool
    {
        return $user !== null
            && $cart->user_id === $user->id
            && $user->getBalance()->value > 0;
    }
}
