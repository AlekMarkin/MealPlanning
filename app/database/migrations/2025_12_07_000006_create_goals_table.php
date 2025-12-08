<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//creates goals table for nutrition targets with period and comparator
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('metric', ['calories', 'protein', 'carbs', 'fat', 'fiber', 'sugar', 'sodium', 'carbon_footprint'])->default('calories');
            $table->decimal('target_value', 10, 2);
            $table->enum('comparator', ['at_most', 'at_least'])->default('at_most');
            $table->enum('period', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->timestamps();

            $table->unique(['user_id', 'metric', 'period']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};