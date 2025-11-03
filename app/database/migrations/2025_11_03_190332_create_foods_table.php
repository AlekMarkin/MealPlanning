<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
Schema::create('foods', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->string('name', 120);
    $table->decimal('calories', 8, 2)->default(0);
    $table->decimal('protein', 8, 2)->default(0);
    $table->decimal('carbs', 8, 2)->default(0);
    $table->decimal('fat', 8, 2)->default(0);
    $table->decimal('fiber', 8, 2)->default(0);
    $table->decimal('sugar', 8, 2)->default(0);
    $table->integer('sodium_mg')->default(0);
    $table->decimal('carbon_footprint_gco2e', 10, 2)->default(0);
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
