<?php

namespace App\Payload\Storefront;

class StorefrontCreateCustomerAddressPayload
{
    public function __construct(
        public string $address1,
        public string $address2,
        public string $city,
        public string $country,
        public string $first_name,
        public string $last_name,
        public string $phone_number,
        public string $postal_code,
        public string $state,
    )
    {
    }
}
