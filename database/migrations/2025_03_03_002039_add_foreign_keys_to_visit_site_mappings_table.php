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
            $table->foreign(['inspection_category_id'])->references(['id'])->on('inspection_types')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['inspection_phase_id'])->references(['id'])->on('inspection_phases')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['phase_option_id'])->references(['id'])->on('inspection_phase_options')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['site_code_id'])->references(['id'])->on('site_codes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['state_id'])->references(['id'])->on('states')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['visit_id'])->references(['id'])->on('visits')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_site_mappings', function (Blueprint $table) {
            $table->dropForeign('visit_site_mappings_inspection_category_id_foreign');
            $table->dropForeign('visit_site_mappings_inspection_phase_id_foreign');
            $table->dropForeign('visit_site_mappings_phase_option_id_foreign');
            $table->dropForeign('visit_site_mappings_site_code_id_foreign');
            $table->dropForeign('visit_site_mappings_state_id_foreign');
            $table->dropForeign('visit_site_mappings_visit_id_foreign');
        });
    }
};
