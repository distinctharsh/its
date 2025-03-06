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
        Schema::create('inspection_category_inspection_category_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('inspection_category_id');
            $table->unsignedBigInteger('inspection_category_type_id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_category_inspection_category_type');
    }
};
