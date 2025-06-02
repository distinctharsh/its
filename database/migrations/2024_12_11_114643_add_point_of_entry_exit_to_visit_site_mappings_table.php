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
        Schema::table('visit_site_mappings', function (Blueprint $table) {
            //
            $table->json('point_of_entry')->nullable()->after('remarks');
            $table->json('point_of_exit')->nullable()->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_site_mappings', function (Blueprint $table) {
            //
            $table->dropColumn('point_of_entry');
            $table->dropColumn('point_of_exit');
        });
    }
};
