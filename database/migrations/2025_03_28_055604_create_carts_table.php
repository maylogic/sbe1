<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('anon_id')->nullable()->index();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\ShippingMethod::class)
                ->nullable()
                ->constrained()->restrictOnDelete();
            $table->foreignIdFor(\App\Models\CustomerAddress::class, 'billing_address_id')
                ->nullable()
                ->constrained()->nullOnDelete();
            $table->foreignIdFor(\App\Models\CustomerAddress::class, 'shipping_address_id')
                ->nullable()
                ->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
