<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('figure_sets')) {
            return;
        }

        Schema::create('figure_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('figure_set_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('set_id');
            $table->string('gender', 1);
            $table->unsignedInteger('club')->default(0);
            $table->boolean('colorable')->default(false);
            $table->boolean('selectable')->default(false);
            $table->boolean('preselectable')->default(false);
            $table->timestamps();

            $table->unique(['figure_set_type_id', 'set_id']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('figure_sets')) {
            return;
        }

        Schema::drop('figure_sets');
    }
};
