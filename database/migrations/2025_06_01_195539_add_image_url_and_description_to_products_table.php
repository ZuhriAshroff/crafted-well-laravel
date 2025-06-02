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
        Schema::table('products', function (Blueprint $table) {
            // Add image_url column if it doesn't exist
            if (!Schema::hasColumn('products', 'image_url')) {
                $table->string('image_url')->nullable()->after('base_formulation_id');
            }
            
            // Add description column if it doesn't exist
            if (!Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('image_url');
            }
            
            // Add is_active column if it doesn't exist
            if (!Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['image_url', 'description', 'is_active']);
        });
    }
};