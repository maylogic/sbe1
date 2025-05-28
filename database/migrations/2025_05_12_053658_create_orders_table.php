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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('anon_id')->nullable()->index();
            $table->string('email')->nullable();
            $table->boolean('apply_balance')->default(false);
            $table->foreignIdFor(\App\Models\User::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\ShippingMethod::class)
                ->nullable()
                ->constrained()->restrictOnDelete();
            $table->foreignIdFor(\App\Models\CustomerAddress::class, 'shipping_address_id')
                ->nullable()
                ->constrained()->nullOnDelete();
            $table->foreignIdFor(\App\Models\PaymentMethod::class)
                ->nullable()
                ->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
