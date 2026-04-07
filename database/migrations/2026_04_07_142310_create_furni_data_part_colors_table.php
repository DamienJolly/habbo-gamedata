<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('furni_data_part_colors')) {
            return;
        }

        Schema::create('furni_data_part_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('furni_data_id')->constrained('furni_data')->cascadeOnDelete();
            $table->string('color_hex', 32);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['furni_data_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('furni_data_part_colors')) {
            return;
        }

        Schema::drop('furni_data_part_colors');
    }
};
