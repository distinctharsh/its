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
        Schema::create('opcw_faxes', function (Blueprint $table) {
            $table->id();
            $table->date('fax_date'); 
            $table->string('fax_number'); 
            $table->string('reference_number'); 
            $table->text('remarks')->nullable();
            $table->boolean('is_active')->default(true); 
            $table->timestamps(); 
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opcw_faxes');
    }
};
