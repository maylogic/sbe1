<?php

namespace App\Services;

use App\Models\CustomerAddress;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CustomerService
{
    public function getCurrentCustomer(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user;
    }

    /**
     * @return Collection<CustomerAddress>|null
     */
    public function getCustomerAddresses(): ?Collection
    {
        return $this->getCurrentCustomer()?->customer_addresses()->get();
    }

    public function createCustomerAddress($data)
    {
        $customer = $this->getCurrentCustomer();
        return CustomerAddress::factory()->create([
            'user_id' => $customer?->id,
            'address1' => $data['address1'],
            'address2' => $data['address2'],
            'city' => $data['city'],
            'country' => $data['country'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_number' => $data['phone_number'],
            'postal_code' => $data['postal_code'],
            'state' => $data['state'],
        ]);
    }
}
