<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Payload\Storefront\StorefrontCustomerLoginPayload;
use App\Payload\Storefront\StorefrontCustomerRegisterPayload;
use App\Services\Storefront\StorefrontCustomerAuthService;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

readonly class RegisterCustomer
{
    public function __construct(private StorefrontCustomerAuthService $authService)
    {
    }

    public function __invoke(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo
    ): User
    {
        $this->authService->register(
            new StorefrontCustomerRegisterPayload(
                $args['email'],
                $args['password'],
                $args['name'],
            )
        );
        return $this->authService->login(new StorefrontCustomerLoginPayload(
            $args['email'],
            $args['password']));
    }
}
