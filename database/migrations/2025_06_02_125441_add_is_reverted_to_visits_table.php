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
        Schema::table('visits', function (Blueprint $table) {
            //
            $table->boolean('is_reverted')->default(false)->after('is_draft');
            $table->timestamp('reverted_at')->nullable()->after('is_reverted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            //
            $table->dropColumn('is_reverted');
            $table->dropColumn('reverted_at');
        });
    }
};
