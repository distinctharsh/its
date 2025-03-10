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
        Schema::create('breadcrumbs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('route_name')->nullable()->unique('route_name');
            $table->string('title');
            $table->string('label');
            $table->integer('parent_id')->nullable()->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breadcrumbs');
    }
};
