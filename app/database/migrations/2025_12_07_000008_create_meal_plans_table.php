<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//creates meal_plans table for scheduling meals by date and type
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('recipe_id')->nullable();
            $table->string('custom_food_name')->nullable();
            $table->date('meal_date');
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'snack']);
            $table->decimal('portion_size', 8, 2)->default(100);
            $table->timestamps();

            $table->index('user_id');
            $table->index('recipe_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_plans');
    }
};