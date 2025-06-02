<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inspectors', function (Blueprint $table) {
            // Drop the unique index on passport_number
            DB::statement('ALTER TABLE inspectors DROP INDEX inspectors_passport_number_unique');

            // Modify columns to LONGTEXT
            $table->longText('name')->change();
            $table->longText('passport_number')->change();
            $table->longText('unlp_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('inspectors', function (Blueprint $table) {
            // Revert to VARCHAR(255)
            $table->string('name', 255)->change();
            $table->string('passport_number', 255)->change();
            $table->string('unlp_number', 255)->nullable()->change();

            // Re-add unique index on passport_number
            $table->unique('passport_number', 'inspectors_passport_number_unique');
        });
    }
};
