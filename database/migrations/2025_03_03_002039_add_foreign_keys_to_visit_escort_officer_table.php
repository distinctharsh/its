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
        Schema::table('visit_escort_officer', function (Blueprint $table) {
            $table->foreign(['escort_officer_id'])->references(['id'])->on('escort_officers')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['visit_id'])->references(['id'])->on('visits')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_escort_officer', function (Blueprint $table) {
            $table->dropForeign('visit_escort_officer_escort_officer_id_foreign');
            $table->dropForeign('visit_escort_officer_visit_id_foreign');
        });
    }
};
