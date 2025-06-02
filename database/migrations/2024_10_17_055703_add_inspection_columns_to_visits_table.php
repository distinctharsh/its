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
        Schema::table('visits', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('inspection_category_id')->nullable()->after('type_of_inspection_id');
            $table->unsignedBigInteger('inspection_category_type_id')->nullable()->after('inspection_category_id');

            $table->foreign('inspection_category_id')->references('id')->on('inspection_categories')->onDelete('set null');
            $table->foreign('inspection_category_type_id')->references('id')->on('inspection_category_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            //
            $table->dropForeign(['inspection_category_id']);
            $table->dropForeign(['inspection_category_type_id']);
            
            // Dropping the columns
            $table->dropColumn(['inspection_category_id', 'inspection_category_type_id']);
        });
    }
};
