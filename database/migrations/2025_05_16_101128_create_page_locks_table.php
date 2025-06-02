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
        Schema::create('page_locks', function (Blueprint $table) {
            $table->id();
            $table->string('page'); // inspector, inspection, opcw, other_staff
            $table->dateTime('from');
            $table->dateTime('to');
            $table->boolean('locked')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_locks');
    }
};
