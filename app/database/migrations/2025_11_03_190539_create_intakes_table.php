<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('intakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('food_id')->nullable()->constrained('foods')->restrictOnDelete();
            $table->foreignId('recipe_id')->nullable()->constrained('recipes')->restrictOnDelete();
            $table->decimal('grams', 10, 2);
            $table->dateTime('consumed_at')->index();
            $table->timestamps();

            // app-level validation will enforce exactly one of food_id or recipe_id is present
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('intakes');
    }
};
