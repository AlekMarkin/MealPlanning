<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('recipes')) {
            Schema::create('recipes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('name', 150);
                $table->timestamps();
            });
            return;
        }

        Schema::table('recipes', function (Blueprint $table) {
            if (!Schema::hasColumn('recipes', 'user_id')) {
                $table->unsignedBigInteger('user_id')->index()->after('id');
            }
            if (!Schema::hasColumn('recipes', 'name')) {
                $table->string('name', 150)->after('user_id');
            }
            if (!Schema::hasColumn('recipes', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        // Non-destructive: don’t drop columns on rollback to avoid losing data.
    }
};
