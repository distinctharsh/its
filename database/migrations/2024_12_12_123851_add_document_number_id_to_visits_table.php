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
            $table->unsignedBigInteger('opcw_document_id')->nullable();
            $table->foreign('opcw_document_id')
            ->references(columns: 'id')
            ->on('opcw_faxes')
            ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropForeign(['opcw_document_id']);
            $table->dropColumn('opcw_document_id');
        });
    }
};
