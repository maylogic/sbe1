<?php

namespace App\Data;

readonly class Price
{
    public function __construct(public int   $regular,
                                public bool  $is_discounted,
                                public float $discounted)
    {
    }

    public function isPositive(): bool
    {
        return $this->regular > 0 || ($this->is_discounted && $this->discounted > 0);
    }

    public function getRegularOrDiscounted(): int
    {
        return $this->is_discounted ? $this->discounted : $this->regular;
    }

    public static function create(int $price, ?float $discounted = null): Price
    {
        if ($discounted === null) $discounted = $price;
        return new Price(
            $price,
            $discounted != $price,
            $discounted
        );
    }
}
