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
        Schema::table('inspectors', function (Blueprint $table) {
            //
            $table->string('ib_clearance')->nullable();    // For IB Clearance document
            $table->string('raw_clearance')->nullable();   // For Raw Clearance document
            $table->string('mea_clearance')->nullable();   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspectors', function (Blueprint $table) {
            //
            $table->dropColumn(['ib_clearance', 'raw_clearance', 'mea_clearance']);
        });
    }
};
