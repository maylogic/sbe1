<?php

namespace App\Enum;

use App\Traits\EnumTrait;

enum UserRole: string
{
    use EnumTrait;

    case ADMIN = 'ADMIN';
    case MANAGER = 'MANAGER';
    case CUSTOMER = 'CUSTOMER';

    public function isAdmin(): bool
    {
        return $this->value == self::ADMIN->value;
    }

    public function isManager(): bool
    {
        return $this->value == self::MANAGER->value;
    }

    public function isCustomer(): bool
    {
        return $this->value == self::CUSTOMER->value;
    }
}
