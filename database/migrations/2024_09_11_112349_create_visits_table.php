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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inspector_id')->nullable();  
            $table->unsignedBigInteger('type_of_inspection_id')->nullable();  
            $table->string('site_of_inspection')->nullable(); 
            $table->string('point_of_entry')->nullable();  
            $table->string('purpose_of_visit')->nullable();  
            $table->dateTime('arrival_datetime')->nullable();  
            $table->json('list_of_inspectors')->nullable();  
            $table->unsignedBigInteger('team_lead_id')->nullable(); 
            $table->dateTime('departure_datetime')->nullable();  
            $table->text('remarks')->nullable(); 
            $table->boolean('is_active')->default(true); 
            $table->timestamps(); 
            $table->softDeletes();
        
            // Foreign keys
            $table->foreign('inspector_id')->references('id')->on('inspectors')->onDelete('cascade');
            $table->foreign('type_of_inspection_id')->references('id')->on('inspection_types')->onDelete('cascade');
            $table->foreign('team_lead_id')->references('id')->on('inspectors')->onDelete('set null');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
