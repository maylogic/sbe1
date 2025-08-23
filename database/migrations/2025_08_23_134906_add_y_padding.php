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
        Schema::table('base_text_areas', function (Blueprint $table) {
            $table->double('padding_y')->unsigned()->default(0.1);
        });
        Schema::table('base_preview_text_areas', function (Blueprint $table) {
            $table->double('padding_y')->unsigned()->default(0.1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
