<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutrition_facts', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            // PK
            $table->bigIncrements('id'); // unsigned BIGINT

            // FK to recipes.id (also unsigned BIGINT)
            $table->unsignedBigInteger('recipe_id');

            // Simple numeric fields (using defaults 8,2 is fine)
            $table->decimal('calories', 8, 2)->nullable();
            $table->decimal('protein', 8, 2)->nullable();
            $table->decimal('carbs', 8, 2)->nullable();
            $table->decimal('fat', 8, 2)->nullable();
            $table->decimal('fiber', 8, 2)->nullable();
            $table->decimal('suger', 8, 2)->nullable(); // keeping your original column name
            $table->decimal('sodium', 8, 2)->nullable();

            $table->timestamps();

            // Index + FK (let Laravel auto-name it)
            $table->index('recipe_id');
            $table->foreign('recipe_id')
                  ->references('id')->on('recipes')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrition_facts');
    }
};
