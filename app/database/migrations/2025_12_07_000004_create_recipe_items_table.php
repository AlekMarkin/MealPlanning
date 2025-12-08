<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//creates recipe_items table linking recipes to foods with gram quantities
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('food_id')->constrained();
            $table->unsignedInteger('grams')->default(0);
            $table->timestamps();

            $table->unique(['recipe_id', 'food_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_items');
    }
};