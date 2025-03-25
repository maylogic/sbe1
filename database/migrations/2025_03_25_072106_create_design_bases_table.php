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
        Schema::create('design_bases', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Design::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Base::class)->constrained()->cascadeOnDelete();
            $table->unsignedInteger('left');
            $table->unsignedInteger('top');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_bases');
    }
};
