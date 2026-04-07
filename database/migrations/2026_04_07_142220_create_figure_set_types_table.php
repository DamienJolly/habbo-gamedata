<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('figure_set_types')) {
            return;
        }

        Schema::create('figure_set_types', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique();
            $table->unsignedInteger('palette_id');
            $table->boolean('mand_m_0')->default(false);
            $table->boolean('mand_f_0')->default(false);
            $table->boolean('mand_m_1')->default(false);
            $table->boolean('mand_f_1')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('figure_set_types')) {
            return;
        }

        Schema::drop('figure_set_types');
    }
};
