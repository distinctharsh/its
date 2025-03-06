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
        Schema::create('inspectors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('gender_id')->index('inspectors_gender_id_foreign');
            $table->date('dob');
            $table->unsignedBigInteger('nationality_id')->index('inspectors_nationality_id_foreign');
            $table->string('place_of_birth');
            $table->string('passport_number')->unique();
            $table->string('unlp_number')->nullable();
            $table->unsignedBigInteger('rank_id')->index('inspectors_rank_id_foreign');
            $table->text('qualifications');
            $table->text('professional_experience');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('designation_id')->nullable()->index('inspectors_designation_id_foreign');
            $table->string('ib_clearance')->nullable();
            $table->string('raw_clearance')->nullable();
            $table->string('mea_clearance')->nullable();
            $table->unsignedBigInteger('ib_status_id')->nullable()->index('fk_ib_status_id');
            $table->unsignedBigInteger('raw_status_id')->nullable()->index('fk_raw_status_id');
            $table->unsignedBigInteger('mea_status_id')->nullable()->index('fk_mea_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspectors');
    }
};
