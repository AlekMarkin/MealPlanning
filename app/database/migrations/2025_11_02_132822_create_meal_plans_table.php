<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_plans', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');

            // FK columns MUST be unsignedBigInteger to match users.id and recipes.id
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('recipe_id');

            $table->date('meal_date');

            // Give real allowed values; empty enum causes errors
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'snack']);

            $table->timestamps();

            // Indexes + FKs
            $table->index('user_id');
            $table->index('recipe_id');

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('recipe_id')
                  ->references('id')->on('recipes')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_plans');
    }
};
