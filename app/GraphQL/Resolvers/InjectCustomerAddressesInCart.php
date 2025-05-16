<?php

namespace App\GraphQL\Resolvers;

use App\Models\Cart;
use App\Models\CustomerAddress;
use App\Services\CustomerService;
use Illuminate\Support\Collection;

readonly class InjectCustomerAddressesInCart
{
    public function __construct(
        private CustomerService $customerService
    )
    {
    }

    /**
     * @param Cart $rootValue
     * @return Collection<CustomerAddress>|null
     */
    public function __invoke(Cart $rootValue): ?Collection
    {
        return $this->customerService->getCustomerAddresses();
    }
}
