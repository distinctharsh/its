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
        Schema::table('visit_site_mappings', function (Blueprint $table) {
            if (!Schema::hasColumn('visit_site_mappings', 'inspection_category_id')) {
                $table->unsignedBigInteger('inspection_category_id')->nullable();
                $table->foreign('inspection_category_id')->references('id')->on('inspection_types')->onDelete('cascade');
            }

            if (!Schema::hasColumn('visit_site_mappings', 'inspection_phase_id')) {
                $table->unsignedBigInteger('inspection_phase_id')->nullable();
                $table->foreign('inspection_phase_id')->references('id')->on('inspection_phases')->onDelete('cascade');
            }

            if (!Schema::hasColumn('visit_site_mappings', 'phase_option_id')) {
                $table->unsignedBigInteger('phase_option_id')->nullable();
                $table->foreign('phase_option_id')->references('id')->on('inspection_phase_options')->onDelete('cascade');
            }

            if (!Schema::hasColumn('visit_site_mappings', 'preliminary_report')) {
                $table->string('preliminary_report')->nullable();
            }

            if (!Schema::hasColumn('visit_site_mappings', 'final_inspection_report')) {
                $table->string('final_inspection_report')->nullable();
            }
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_site_mappings', function (Blueprint $table) {
            if (Schema::hasColumn('visit_site_mappings', 'inspection_category_id')) {
                $table->dropForeign(['inspection_category_id']);
                $table->dropColumn('inspection_category_id');
            }
    
            if (Schema::hasColumn('visit_site_mappings', 'inspection_phase_id')) {
                $table->dropForeign(['inspection_phase_id']);
                $table->dropColumn('inspection_phase_id');
            }
    
            if (Schema::hasColumn('visit_site_mappings', 'phase_option_id')) {
                $table->dropForeign(['phase_option_id']);
                $table->dropColumn('phase_option_id');
            }
    
            if (Schema::hasColumn('visit_site_mappings', 'preliminary_report')) {
                $table->dropColumn('preliminary_report');
            }
    
            if (Schema::hasColumn('visit_site_mappings', 'final_inspection_report')) {
                $table->dropColumn('final_inspection_report');
            }
        });
    }
};
