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
        Schema::create('base_text_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Base::class)->constrained()->cascadeOnDelete();
            $table->char('text_color', 7)->nullable();
            $table->boolean('inherit_color')->default(false);
            $table->double('width')->unsigned();
            $table->double('height')->unsigned();
            $table->double('left')->unsigned();
            $table->double('top')->unsigned();
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
        Schema::dropIfExists('base_text_areas');
    }
};
