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
        Schema::create('order_item_text_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\OrderItem::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\ProductLineDefinition::class)->constrained()->restrictOnDelete();
            $table->string('value');
            $table->timestamps();
            $table->unique(['order_item_id', 'product_line_definition_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_text_lines');
    }
};
