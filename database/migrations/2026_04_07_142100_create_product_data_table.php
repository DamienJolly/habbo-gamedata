<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_data')) {
            return;
        }

        Schema::create('product_data', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->text('name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_data')) {
            return;
        }

        Schema::drop('product_data');
    }
};
