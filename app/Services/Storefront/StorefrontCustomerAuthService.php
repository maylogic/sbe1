<?php

namespace App\Services\Storefront;

use App\Models\User;
use App\Payload\Storefront\StorefrontCustomerLoginPayload;
use App\Payload\Storefront\StorefrontCustomerRegisterPayload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StorefrontCustomerAuthService
{
    public function register(StorefrontCustomerRegisterPayload $payload)
    {
        return User::create(
            [
                'email' => $payload->email,
                'password' => Hash::make($payload->password),
                'name' => $payload->name,
            ]
        );
    }

    public function login(StorefrontCustomerLoginPayload $payload): User
    {
        if (!Auth::attempt(
            [
                'email' => $payload->email,
                'password' => $payload->password
            ]
        )) {
            throw new \InvalidArgumentException('Invalid credentials');
        }

        /** @var User $user */
        $user = Auth::user();
        return $user;
    }
}
