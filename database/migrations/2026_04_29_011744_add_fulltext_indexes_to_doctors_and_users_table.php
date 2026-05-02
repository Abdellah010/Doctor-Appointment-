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
        if (config('database.default') !== 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->fullText('name');
            });

            Schema::table('doctors', function (Blueprint $table) {
                $table->fullText('specialty');
            });
        }
    }

    public function down(): void
    {
        if (config('database.default') !== 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropFullText(['name']);
            });

            Schema::table('doctors', function (Blueprint $table) {
                $table->dropFullText(['specialty']);
            });
        }
    }
};
