<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class BalanceService
{
    public function canApplyBalance(Cart|Order $cartOrOrder): bool
    {
        if ($cartOrOrder instanceof Order) {
            return true;
        }
        $user = Auth::user();
        if ($user) {
            return $cartOrOrder->user_id === $user->id
                && $user->getBalance()->value > 0;
        }
        return false;
    }
}
