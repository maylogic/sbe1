<?php

namespace App\Services;

use App\Models\CustomerAddress;
use App\Models\User;
use App\Payload\Storefront\StorefrontCreateCustomerAddressPayload;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

readonly class CustomerService
{
    public function __construct(private CartService $cartService)
    {
    }

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

    public function createCustomerAddress(StorefrontCreateCustomerAddressPayload $payload)
    {
        $customer = $this->getCurrentCustomer();
        return CustomerAddress::factory()->create([
            'user_id' => $customer?->id,
            'address1' => $payload->address1,
            'address2' => $payload->address2,
            'city' => $payload->city,
            'country' => $payload->country,
            'first_name' => $payload->first_name,
            'last_name' => $payload->last_name,
            'phone_number' => $payload->phone_number,
            'postal_code' => $payload->postal_code,
            'state' => $payload->state,
        ]);
    }

    public function updateCustomerAddress(int $customerAddressId, StorefrontCreateCustomerAddressPayload $payload): void
    {
        $customer = $this->getCurrentCustomer();
        $customerAddress = CustomerAddress::find($customerAddressId);
        $cart = $this->cartService->getCurrentCart();
        if (!$customerAddress) {
            return;
        }
        if ($customer && $customer->id !== $customerAddress->user_id) {
            return;
        }
        if (!$customer && $cart->shipping_address_id != $customerAddress->id) {
            return;
        }
        $customerAddress->update([
            'address1' => $payload->address1,
            'address2' => $payload->address2,
            'city' => $payload->city,
            'country' => $payload->country,
            'first_name' => $payload->first_name,
            'last_name' => $payload->last_name,
            'phone_number' => $payload->phone_number,
            'postal_code' => $payload->postal_code,
            'state' => $payload->state,
        ]);
        $cart->load(['shipping_address']);
    }
}
