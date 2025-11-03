<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->cascadeOnDelete();
            $table->foreignId('food_id')->constrained('foods')->restrictOnDelete();
            $table->decimal('grams', 10, 2); // how much of the food in the recipe
            $table->timestamps();

            $table->unique(['recipe_id','food_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('recipe_items');
    }
};
