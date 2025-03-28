<?php

namespace App\GraphQL\Mutations;

use App\Payload\Storefront\StorefrontCustomerRegisterPayload;
use App\Services\Storefront\StorefrontCustomerAuthService;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

readonly class RegisterCustomer
{
    public function __construct(private StorefrontCustomerAuthService $authService) {}

    public function __invoke(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo
    )
    {
        return $this->authService->register(
            new StorefrontCustomerRegisterPayload(
                $args['email'],
                $args['password'],
                $args['name'],
            )
        );
    }
}
