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
        Schema::create('visit_site_mappings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('visit_id')->nullable()->index();
            $table->unsignedBigInteger('site_code_id')->nullable()->index();
            $table->text('site_of_inspection')->nullable();
            $table->unsignedBigInteger('state_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('inspection_category_id')->nullable()->index('visit_site_mappings_inspection_category_id_foreign');
            $table->unsignedBigInteger('inspection_phase_id')->nullable()->index('visit_site_mappings_inspection_phase_id_foreign');
            $table->unsignedBigInteger('phase_option_id')->nullable()->default(1)->index('visit_site_mappings_phase_option_id_foreign');
            $table->string('preliminary_report')->nullable();
            $table->string('final_inspection_report')->nullable();
            $table->integer('inspection_issue_id')->nullable();
            $table->string('issue_document')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_site_mappings');
    }
};
