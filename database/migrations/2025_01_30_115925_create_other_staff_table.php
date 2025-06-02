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
        if (!Schema::hasTable('other_staff')) {
            Schema::create('other_staff', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->foreignId('gender_id')->nullable()->constrained('genders')->onDelete('cascade');
                $table->date('dob')->nullable();
                $table->string('place_of_birth')->nullable();
                $table->foreignId('nationality_id')->nullable()->constrained('nationalities')->onDelete('cascade');
                $table->string('unlp_number')->nullable();
                $table->string('passport_number')->unique()->nullable();
                $table->foreignId('designation_id')->nullable()->constrained('designations')->onDelete('cascade');
                $table->foreignId('rank_id')->nullable()->constrained('ranks')->onDelete('cascade');
                $table->string('qualifications')->nullable();
                $table->string('professional_experience')->nullable();
    
                // Set these to nullable to avoid issues
                $table->string('scope_of_access')->nullable();
                $table->string('security_status')->nullable();
    
                $table->date('opcw_communication_date')->nullable();
    
                $table->date('deletion_date')->nullable();
                $table->text('remarks')->nullable();
    
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('other_staff')) {
            Schema::dropIfExists('other_staff');
        }
    }
};
