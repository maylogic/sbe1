<?php

namespace App\GraphQL\Queries;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class GetCart
{
    const SESSION_CART_ANON_ID_KEY = 'cart_anon_id';

    public function __invoke(
        $rootValue,
        array $args
    ): Product
    {
        // retrieve or create user cart
        $userId = Auth::user()?->id;
        if ($userId) {
            return Cart::whereUserId($userId)->firstOrCreate([
                'user_id' => $userId
            ]);
        }
        // retrieve or create cart for existing anon key
        $cartAnonId = session(self::SESSION_CART_ANON_ID_KEY);
        if ($cartAnonId) {
            return Cart::whereAnonId($cartAnonId)->firstOrCreate([
                'anon_id' => $cartAnonId
            ]);
        }
        // create new anon key and cart
        $cartAnonId = uuid_create();
        session([self::SESSION_CART_ANON_ID_KEY => $cartAnonId]);
        return Cart::factory()->create([
            'anon_id' => $cartAnonId
        ]);
    }
}
