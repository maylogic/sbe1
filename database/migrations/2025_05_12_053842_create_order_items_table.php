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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Product::class)->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->unsignedInteger('price_regular');
            $table->unsignedInteger('price_discounted')->nullable();
            $table->foreignIdFor(\App\Models\Design::class)
                ->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(\App\Models\Color::class)
                ->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(\App\Models\Font::class)
                ->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(\App\Models\ProductSize::class)
                ->nullable()->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
