<?php

namespace App\Data;

class Price
{
    public function __construct(public int  $regular,
                                public bool $is_discounted,
                                public int  $discounted)
    {
    }
}
