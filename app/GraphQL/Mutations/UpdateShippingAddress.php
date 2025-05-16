<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Payload\Storefront\StorefrontCreateCustomerAddressPayload;
use App\Services\CartService;
use App\Services\CustomerService;

readonly class UpdateShippingAddress
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
        $id = intval($args['customerAddressId']);
        $this->customerService->updateCustomerAddress(
            $id,
            new StorefrontCreateCustomerAddressPayload(
                $args['address1'],
                $args['address2'],
                $args['city'],
                $args['country'],
                $args['first_name'],
                $args['last_name'],
                $args['phone_number'],
                $args['postal_code'],
                $args['state']
            )
        );
        return $this->cartService->getCurrentCart();
    }
}
