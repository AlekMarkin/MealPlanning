<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('carbon_footprints', function (Blueprint $table) {
            $table->id()->foreign('recipes.id');
            $table->bigInteger('recipe_id');
            $table->decimal('carbon_grams');
            $table->bigInteger('water_usage_liters');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carbon_footprints');
    }
};
