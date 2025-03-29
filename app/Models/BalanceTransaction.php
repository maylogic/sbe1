<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $balance_id
 * @property int $value
 * @property string $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\BalanceTransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereBalanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereValue($value)
 * @mixin \Eloquent
 */
class BalanceTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\BalanceTransactionFactory> */
    use HasFactory;
}
