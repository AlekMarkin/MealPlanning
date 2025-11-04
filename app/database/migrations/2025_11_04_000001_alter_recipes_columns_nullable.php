<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            if (!Schema::hasColumn('recipes', 'title')) {
                $table->string('title')->nullable()->after('name');
            }
            if (!Schema::hasColumn('recipes', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            // Some dumps use either "instructions" or a typo "instructure" — create both as nullable.
            if (!Schema::hasColumn('recipes', 'instructions')) {
                $table->longText('instructions')->nullable()->after('description');
            }
            if (!Schema::hasColumn('recipes', 'instructure')) {
                $table->longText('instructure')->nullable()->after('instructions');
            }
        });
    }

    public function down(): void
    {
        // No-op on down to avoid data loss
    }
};
