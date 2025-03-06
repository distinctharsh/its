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
        Schema::create('other_staff', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->unsignedBigInteger('gender_id')->nullable()->index('gender_id');
            $table->date('dob')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->unsignedBigInteger('nationality_id')->nullable()->index('nationality_id');
            $table->string('unlp_number')->nullable();
            $table->string('passport_number')->nullable()->unique('passport_number');
            $table->unsignedBigInteger('designation_id')->nullable()->index('designation_id');
            $table->unsignedBigInteger('rank_id')->nullable()->index('rank_id');
            $table->text('qualifications')->nullable();
            $table->text('professional_experience')->nullable();
            $table->string('scope_of_access')->nullable();
            $table->string('security_status')->nullable();
            $table->date('opcw_communication_date')->nullable();
            $table->unsignedBigInteger('opcw_document_id')->nullable()->index('opcw_document_id');
            $table->date('deletion_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_staff');
    }
};
