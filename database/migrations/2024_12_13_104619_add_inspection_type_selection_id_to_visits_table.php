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
            if (!Schema::hasColumn('visits', 'inspection_property_id')) {
                $table->unsignedBigInteger('inspection_property_id')->nullable();
                $table->foreign('inspection_property_id')->references('id')->on('inspection_properties')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('inspection_property_id')->nullable();
            $table->foreign('inspection_property_id')->references('id')->on('inspection_properties')->onDelete('set null');
        });
    }
};
