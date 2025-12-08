<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//creates intakes table for tracking daily food and recipe consumption
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('intake_date');
            $table->foreignId('food_id')->nullable()->constrained();
            $table->foreignId('recipe_id')->nullable()->constrained();
            $table->integer('quantity_g')->default(0);
            $table->decimal('servings', 8, 2)->default(0);
            $table->datetime('consumed_at')->nullable();
            $table->timestamps();

            $table->index('intake_date');
            $table->index('consumed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intakes');
    }
};