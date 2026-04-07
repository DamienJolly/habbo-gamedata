<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('figure_palettes')) {
            return;
        }

        Schema::create('figure_palettes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('palette_id')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('figure_palettes')) {
            return;
        }

        Schema::drop('figure_palettes');
    }
};
