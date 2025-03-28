<?php

namespace App\Payload\Storefront;

class StorefrontCustomerRegisterPayload
{
    public function __construct(
        public string $email,
        public string $password,
        public string $name,
    )
    {
    }
}
