<?php

namespace App\Payload\Storefront;

class StorefrontCreateCustomerAddressPayload
{
    /**
     * Every field is nullable in the GraphQL schema and in `customer_addresses`,
     * so the payload must accept nulls too: a guest who fills in only city and
     * street must not trigger a TypeError.
     */
    public function __construct(
        public ?string $address1,
        public ?string $address2,
        public ?string $city,
        public ?string $country,
        public ?string $first_name,
        public ?string $last_name,
        public ?string $phone_number,
        public ?string $postal_code,
        public ?string $state,
    )
    {
    }
}
