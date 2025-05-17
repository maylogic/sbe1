<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Payload\Storefront\StorefrontCustomerLoginPayload;
use App\Services\CartService;
use App\Services\Storefront\StorefrontCustomerAuthService;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

readonly class LoginCustomer
{
    public function __construct(
        private CartService                   $cartService,
        private StorefrontCustomerAuthService $authService
    )
    {
    }

    public function __invoke(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo
    ): User
    {
        $anonCart = $this->cartService->getAnonCart();
        $anonCartEmpty = $anonCart === null || $anonCart->isEmpty();
        $user = $this->authService->login(
            new StorefrontCustomerLoginPayload(
                $args['email'],
                $args['password']
            )
        );
        $userCart = $this->cartService->getUserCart($user->id);
        $userCartEmpty = $userCart === null || $userCart->isEmpty();

        $anonCartFresher = $anonCart?->isFresherThan($userCart);
        $userCartPreferred = !$userCartEmpty && !$anonCartFresher;
        $shouldDropAnonCart = $anonCartEmpty || $userCartPreferred;
        if ($shouldDropAnonCart) {
            $anonCart?->delete();
        } else {
            $userCart?->delete();
            $this->cartService->attachCartToUser($anonCart, $user);
        }
        return $user;
    }
}
