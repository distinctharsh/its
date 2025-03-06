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
            $table->foreign(['inspection_category_id'])->references(['id'])->on('inspection_categories')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['inspection_category_type_id'])->references(['id'])->on('inspection_category_types')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['inspection_property_id'])->references(['id'])->on('inspection_properties')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['inspector_id'])->references(['id'])->on('inspectors')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['opcw_document_id'])->references(['id'])->on('opcw_faxes')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['site_code_id'])->references(['id'])->on('site_codes')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['state_id'])->references(['id'])->on('states')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['team_lead_id'])->references(['id'])->on('inspectors')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['type_of_inspection_id'])->references(['id'])->on('inspection_types')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropForeign('visits_inspection_category_id_foreign');
            $table->dropForeign('visits_inspection_category_type_id_foreign');
            $table->dropForeign('visits_inspection_property_id_foreign');
            $table->dropForeign('visits_inspector_id_foreign');
            $table->dropForeign('visits_opcw_document_id_foreign');
            $table->dropForeign('visits_site_code_id_foreign');
            $table->dropForeign('visits_state_id_foreign');
            $table->dropForeign('visits_team_lead_id_foreign');
            $table->dropForeign('visits_type_of_inspection_id_foreign');
        });
    }
};
