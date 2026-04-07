<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('figure_parts')) {
            return;
        }

        Schema::create('figure_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('figure_set_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('part_id');
            $table->string('type');
            $table->boolean('colorable')->default(false);
            $table->unsignedInteger('index')->default(0);
            $table->unsignedInteger('colorindex')->default(0);
            $table->timestamps();

            $table->unique(['figure_set_id', 'part_id', 'type']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('figure_parts')) {
            return;
        }

        Schema::drop('figure_parts');
    }
};
