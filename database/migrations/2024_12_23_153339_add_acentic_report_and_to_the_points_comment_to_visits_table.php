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
            if (!Schema::hasColumn('visits', 'acentric_report')) {
                $table->text('acentric_report')->nullable()->after('remarks');
            }
            if (!Schema::hasColumn('visits', 'to_the_points_comment')) {
                $table->text('to_the_points_comment')->nullable()->after('acentric_report');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            if (Schema::hasColumn('visits', 'acentric_report')) {
                $table->dropColumn('acentric_report');
            }
            if (Schema::hasColumn('visits', 'to_the_points_comment')) {
                $table->dropColumn('to_the_points_comment');
            }
        });
    }
};
