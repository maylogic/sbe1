<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Services\CartService;
use App\Services\CouponService;

readonly class SetCartEmail
{
    public function __construct(
        private CartService   $cartService,
        private CouponService $couponService
    )
    {
    }

    public function __invoke(
        $rootValue,
        array $args
    ): Cart
    {
        $email = $args['email'];
        // Может ли пользователь использовать этот email?
        // Я так думаю что при place order нужно проверять что кастомер с таким email существует
        // Если существует то не позволять создать ордер пока кастомер не войдет
        // Если не существует, то выводить галочку зарегаться и кнопки зарегаться/продолжить как гость

        // если пользователь не помнит пароль должна быть возможность его восстановить через код,
        // либо указать пользователю что он может использовать другой email
        $cart = $this->cartService->getCurrentCart();
        $cart->setEmail($email);
        return $cart;
    }
}
