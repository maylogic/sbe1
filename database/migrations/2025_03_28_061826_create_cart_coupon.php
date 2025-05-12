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
        Schema::create('cart_coupon', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Cart::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Coupon::class)->constrained()->restrictOnDelete();
            $table->unique(['cart_id', 'coupon_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_coupon');
    }
};
