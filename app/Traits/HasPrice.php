<?php

namespace App\Traits;

trait HasPrice
{
    public function getPrice(): int
    {
        return $this->price;
    }
}
