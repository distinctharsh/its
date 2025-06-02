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
            $table->id();
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->unsignedBigInteger('site_code_id')->nullable();
            $table->text('site_of_inspection')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('visit_id');
            $table->index('site_code_id');
            $table->index('state_id');

            // Foreign key constraints
            $table->foreign('visit_id')->references('id')->on('visits')->onDelete('cascade');
            $table->foreign('site_code_id')->references('id')->on('site_codes')->onDelete('cascade');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
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
