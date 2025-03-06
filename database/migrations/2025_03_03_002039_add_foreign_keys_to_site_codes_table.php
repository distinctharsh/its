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
        Schema::table('site_codes', function (Blueprint $table) {
            $table->foreign(['state_id'])->references(['id'])->on('states')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_codes', function (Blueprint $table) {
            $table->dropForeign('site_codes_state_id_foreign');
        });
    }
};
