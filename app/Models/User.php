<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enum\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'coupon_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class
        ];
    }

    public function customer_addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function balance(): HasOne
    {
        return $this->hasOne(Balance::class);
    }

    public function adminPanelAllowed(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    public function isAdmin(): bool
    {
        return $this->role->isAdmin();
    }

    public function isManager(): bool
    {
        return $this->role->isManager();
    }

    public function isCustomer(): bool
    {
        return $this->role->isCustomer();
    }

    public function getBalance(): Balance
    {
        if (!$this->balance) {
            $this->balance()->create();
            $this->load(['balance']);
        }
        return $this->balance;
    }
}
