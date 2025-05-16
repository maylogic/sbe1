<?php

namespace App\GraphQL\Mutations;

use App\Models\Cart;
use App\Models\CustomerAddress;
use App\Services\CartService;
use App\Services\CustomerService;

readonly class SetShippingAddress
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
        $customerId = $this->customerService->getCurrentCustomer()?->id;
        if ($customerId) {
            $address = CustomerAddress::find($id);
            if ($address?->user_id == $customerId) {
                return $this->cartService->updateShippingAddress($id);
            }
        }
        return $this->cartService->getCurrentCart();
    }
}
