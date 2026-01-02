<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesRule extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'ends_at' => 'datetime'
    ];
}
