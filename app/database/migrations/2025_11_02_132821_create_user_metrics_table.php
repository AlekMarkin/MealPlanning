<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_metrics', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');            // unsigned BIGINT
            $table->unsignedBigInteger('user_id');  // MUST be unsigned to match users.id

            $table->date('recorded_date');
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->integer('bp_systolic')->nullable();
            $table->integer('bp_diastolic')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_metrics');
    }
};
