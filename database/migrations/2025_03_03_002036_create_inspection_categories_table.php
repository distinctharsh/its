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
        Schema::create('inspection_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('category_name');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('is_challenge')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_categories');
    }
};
