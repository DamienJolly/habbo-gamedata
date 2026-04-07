<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('furni_data')) {
            return;
        }

        Schema::create('furni_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sprite_id');
            $table->string('item_type', 1)->default('s');
            $table->string('item_name');
            $table->unsignedInteger('revision')->default(0);
            $table->string('category')->nullable();
            $table->integer('default_direction')->nullable();
            $table->integer('length')->nullable();
            $table->integer('width')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('ad_url')->nullable();
            $table->integer('offer_id')->default(-1);
            $table->boolean('buyout')->default(false);
            $table->integer('rent_offer_id')->default(-1);
            $table->boolean('rent_buyout')->default(false);
            $table->boolean('bc')->default(false);
            $table->integer('excluded_dynamic')->default(0);
            $table->integer('bc_offer_id')->default(-1);
            $table->text('custom_params')->nullable();
            $table->integer('special_type')->default(0);
            $table->boolean('can_stand_on')->nullable();
            $table->boolean('can_sit_on')->nullable();
            $table->boolean('can_lay_on')->nullable();
            $table->string('furni_line')->nullable();
            $table->string('environment')->nullable();
            $table->boolean('rare')->default(false);
            $table->boolean('tradeable')->default(false);
            $table->timestamps();

            $table->unique(['item_type', 'sprite_id']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('furni_data')) {
            return;
        }

        Schema::drop('furni_data');
    }
};
