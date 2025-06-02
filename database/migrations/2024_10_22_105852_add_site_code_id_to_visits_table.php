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
            $table->unsignedBigInteger('site_code_id')->nullable();
            $table->foreign('site_code_id')->references('id')->on('site_codes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            //
            $table->dropForeign(['site_code_id']);
            $table->dropColumn('site_code_id');
        });
    }
};
