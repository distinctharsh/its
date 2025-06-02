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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inspector_id'); 
            $table->unsignedBigInteger('category_id');
            $table->date('date_of_joining'); 
            $table->unsignedBigInteger('status_id'); 
            $table->text('remarks')->nullable();
            $table->char('code', 1)->after('remarks'); 

            $table->foreign('inspector_id')->references('id')->on('inspectors')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('inspection_categories')->onDelete('cascade'); 
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->timestamps(); 
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
        
    }
};
