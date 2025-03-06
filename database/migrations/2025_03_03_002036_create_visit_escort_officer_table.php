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
        Schema::create('visit_escort_officer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('visit_id')->index('visit_escort_officer_visit_id_foreign');
            $table->unsignedBigInteger('escort_officer_id')->index('visit_escort_officer_escort_officer_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_escort_officer');
    }
};
