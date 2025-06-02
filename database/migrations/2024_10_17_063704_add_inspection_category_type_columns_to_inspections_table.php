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
            $table->unsignedBigInteger('category_type_id')->nullable()->after('category_id');
            $table->foreign('category_type_id')->references('id')->on('inspection_category_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            //
            $table->dropForeign(['category_type_id']);
            $table->dropColumn(['category_type_id']);
        });
    }
};
