<?php

namespace App\Payload\Storefront;

class StorefrontCustomerLoginPayload
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}
