<?php

namespace App\Data\Abstract;

abstract readonly class PriceContext
{
    public string $key;

    public function __construct()
    {
        $this->key = $this->key();
    }

    abstract public function toArray(): array;

    abstract public function key(): string;
}
