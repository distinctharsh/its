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
        Schema::table('inspection_categories', function (Blueprint $table) {
            //
            $table->boolean('is_challenge')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspection_categories', function (Blueprint $table) {
            //
            $table->dropColumn('is_challenge');
        });
    }
};
