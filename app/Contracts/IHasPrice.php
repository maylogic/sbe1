<?php

namespace App\Contracts;

interface IHasPrice
{
    public function getPrice(): int;
}
