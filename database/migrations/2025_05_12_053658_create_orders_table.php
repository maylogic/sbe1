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
            $table->string('comment')->nullable();
            $table->enum('status', \App\Enum\OrderStatus::values())->default(
                \App\Enum\OrderStatus::PENDING->value
            );
            $table->boolean('apply_balance')->default(false);
            $table->foreignIdFor(\App\Models\User::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\ShippingMethod::class)
                ->constrained()->restrictOnDelete();
            $table->foreignIdFor(\App\Models\CustomerAddress::class, 'shipping_address_id')
                ->constrained()->restrictOnDelete();
            $table->unsignedInteger('shipping_price_regular');
            $table->unsignedInteger('shipping_price_discounted');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('grand_total_regular');
            $table->unsignedInteger('grand_total_discounted');
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
