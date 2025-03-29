<?php

namespace App\Data;

class Totals
{
    public function __construct(
        public int $subtotal,
        public Price $grand_total,
        public int $points_used
    )
    {
    }
}
