<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->after('name');
            $table->string('last_name')->after('first_name');
        });

        // Migrate existing data
        DB::table('users')->update([
            'first_name' => DB::raw('SUBSTRING_INDEX(name, " ", 1)'),
            'last_name' => DB::raw('SUBSTRING(name, LENGTH(SUBSTRING_INDEX(name, " ", 1)) + 2)')
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
        });

        // Migrate data back
        DB::table('users')->update([
            'name' => DB::raw('CONCAT(first_name, " ", last_name)')
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
