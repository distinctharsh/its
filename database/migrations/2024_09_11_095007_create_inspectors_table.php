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
        if (!Schema::hasTable('inspectors')) {
            Schema::create('inspectors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('gender_id')->constrained('genders')->onDelete('cascade');
                $table->date('dob');
                $table->foreignId('nationality_id')->constrained('nationalities')->onDelete('cascade');
                $table->string('place_of_birth');
                $table->string('passport_number')->unique();
                $table->string('unlp_number')->nullable();
                $table->foreignId('rank_id')->constrained('ranks')->onDelete('cascade');
                $table->foreignId('designation_id')->constrained('designations')->onDelete('cascade');
                $table->string('qualifications');
                $table->string('professional_experience');
                $table->text('remarks')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->softDeletes();
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspectors');
    }
};
