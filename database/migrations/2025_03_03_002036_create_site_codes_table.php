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
        Schema::create('site_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('site_code');
            $table->string('site_name');
            $table->text('site_address');
            $table->unsignedBigInteger('state_id')->nullable()->index('site_codes_state_id_foreign');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_codes');
    }
};
