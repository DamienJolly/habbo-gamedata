<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('figure_palette_colors')) {
            return;
        }

        Schema::create('figure_palette_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('figure_palette_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('color_id');
            $table->unsignedInteger('index')->default(0);
            $table->unsignedInteger('club')->default(0);
            $table->boolean('selectable')->default(true);
            $table->string('hex_code', 6);
            $table->timestamps();

            $table->unique(['figure_palette_id', 'color_id']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('figure_palette_colors')) {
            return;
        }

        Schema::drop('figure_palette_colors');
    }
};
