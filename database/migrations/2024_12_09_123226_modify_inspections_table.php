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
        Schema::table('inspections', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('objection_department_id')->nullable()->after('remarks');
            $table->foreign('objection_department_id')->references('id')->on('departments')->onDelete('cascade');

            $table->string('routine_objection_document')->nullable()->after('objection_department_id');
            $table->string('challenge_objection_document')->nullable()->after('objection_department_id');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            //
            $table->dropForeign(['objection_department_id']);
            
            // Dropping the columns
            $table->dropColumn(['objection_department_id', 'routine_objection_document', 'challenge_objection_document']);
        });
    }
};
