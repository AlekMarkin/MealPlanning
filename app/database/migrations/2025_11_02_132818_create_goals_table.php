<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            // PK
            $table->bigIncrements('id'); // unsigned BIGINT

            // FK to users.id (also unsigned BIGINT)
            $table->unsignedBigInteger('user_id');

            // Very simple columns (student level)
            $table->string('name', 100);     // e.g. protein, fiber, calories
            $table->string('direction', 10); // 'up' or 'down'
            $table->integer('target_value'); // keep as int for now

            $table->timestamps();

            // Index + FK
            $table->index('user_id');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
