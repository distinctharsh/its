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
            if (!Schema::hasColumn('visits', 'inspection_phase_id')) {
                $table->unsignedBigInteger('inspection_phase_id')->nullable();
                $table->foreign('inspection_phase_id')->references('id')->on('inspection_phases')->onDelete('cascade');
            }

            // Add 'phase_option_id' column
            if (!Schema::hasColumn('visits', 'phase_option_id')) {
                $table->unsignedBigInteger('phase_option_id')->nullable();
                $table->foreign('phase_option_id')->references('id')->on('phase_options')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            //
            if (Schema::hasColumn('visits', 'inspection_phase_id')) {
                $table->dropForeign(['inspection_phase_id']);
            }

            if (Schema::hasColumn('visits', 'phase_option_id')) {
                $table->dropForeign(['phase_option_id']);
            }

            // Drop the columns if they exist
            $table->dropColumn(['inspection_phase_id', 'phase_option_id']);
        });
    }
};
