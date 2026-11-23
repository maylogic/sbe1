<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Payload\Storefront\StorefrontCreateCustomerAddressPayload;
use App\Services\CartService;
use App\Services\CustomerService;

readonly class CreateShippingAddress
{
    public function __construct(
        private CartService     $cartService,
        private CustomerService $customerService
    )
    {
    }

    public function __invoke(
        $rootValue,
        array $args
    ): Cart
    {
        $address = $this->customerService->createCustomerAddress(
            new StorefrontCreateCustomerAddressPayload(
                $args['address1'] ?? null,
                $args['address2'] ?? null,
                $args['city'] ?? null,
                $args['country'] ?? null,
                $args['first_name'] ?? null,
                $args['last_name'] ?? null,
                $args['phone_number'] ?? null,
                $args['postal_code'] ?? null,
                $args['state'] ?? null
            )
        );
        return $this->cartService->updateShippingAddress($address->id);
    }
}
