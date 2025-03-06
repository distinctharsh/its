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
        Schema::create('inspections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('inspector_id')->index('inspections_inspector_id_foreign');
            $table->unsignedBigInteger('category_id')->index('inspections_category_id_foreign');
            $table->unsignedBigInteger('category_type_id')->nullable()->index('inspections_category_type_id_foreign');
            $table->date('date_of_joining');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('objection_department_id')->nullable()->index('inspections_objection_department_id_foreign');
            $table->string('challenge_objection_document')->nullable();
            $table->string('routine_objection_document')->nullable();
            $table->date('deletion_date')->nullable();
            $table->string('purpose_of_deletion')->nullable();
            $table->char('code', 1)->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index('inspections_created_by_foreign');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
