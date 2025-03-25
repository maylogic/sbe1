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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Product::class)->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->foreignIdFor(\App\Models\ProductDesign::class)
                ->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(\App\Models\Color::class)
                ->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(\App\Models\Font::class)
                ->nullable()->constrained()->restrictOnDelete();
            $table->json('text_lines')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
