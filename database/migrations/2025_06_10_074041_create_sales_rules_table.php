<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_rules', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('amount');
            $table->boolean('is_percentage');
            $table->boolean('active');
            $table->boolean('product');
            $table->boolean('shipping');
            $table->string('expression')->nullable();
            $table->unsignedInteger('sort');
            $table->timestamp('ends_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_rules');
    }
};
