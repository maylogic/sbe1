<?php

namespace App\GraphQL\Resolvers;

readonly class UniqueKey
{
    public function __invoke(): string
    {
        return uniqid();
    }
}
