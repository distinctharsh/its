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
        Schema::create('visits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('inspector_id')->index('visits_inspector_id_foreign');
            $table->unsignedBigInteger('type_of_inspection_id')->index('visits_type_of_inspection_id_foreign');
            $table->unsignedBigInteger('inspection_category_id')->nullable()->index('visits_inspection_category_id_foreign');
            $table->unsignedBigInteger('inspection_category_type_id')->nullable()->index('visits_inspection_category_type_id_foreign');
            $table->string('inspection_type_selection')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('visit_report')->nullable();
            $table->string('clearance_certificate')->nullable();
            $table->string('site_of_inspection');
            $table->string('purpose_of_visit')->nullable();
            $table->dateTime('arrival_datetime');
            $table->text('list_of_inspectors')->nullable();
            $table->longText('list_of_escort_officers')->nullable();
            $table->unsignedBigInteger('team_lead_id')->nullable()->index('visits_team_lead_id_foreign');
            $table->unsignedBigInteger('state_id')->nullable()->index('visits_state_id_foreign');
            $table->dateTime('departure_datetime');
            $table->text('remarks')->nullable();
            $table->text('acentric_report')->nullable();
            $table->text('to_the_points_comment')->nullable();
            $table->longText('point_of_exit')->nullable();
            $table->longText('point_of_entry')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('site_code_id')->nullable()->index('visits_site_code_id_foreign');
            $table->unsignedBigInteger('inspection_phase_id')->nullable()->index('visits_inspection_phase_id_foreign');
            $table->unsignedBigInteger('phase_option_id')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->unsignedBigInteger('opcw_document_id')->nullable()->index('visits_opcw_document_id_foreign');
            $table->unsignedBigInteger('inspection_property_id')->nullable()->index('visits_inspection_property_id_foreign');
            $table->longText('escort_officers_poe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
