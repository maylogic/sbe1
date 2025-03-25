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
        Schema::create('design_text_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Design::class)->constrained()->cascadeOnDelete();
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->unsignedInteger('left');
            $table->unsignedInteger('top');
            $table->unsignedTinyInteger('first_line');
            $table->unsignedTinyInteger('lines');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_text_areas');
    }
};
