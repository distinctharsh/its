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
        Schema::table('breadcrumbs', function (Blueprint $table) {
            $table->foreign(['parent_id'], 'breadcrumbs_ibfk_1')->references(['id'])->on('breadcrumbs')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breadcrumbs', function (Blueprint $table) {
            $table->dropForeign('breadcrumbs_ibfk_1');
        });
    }
};
