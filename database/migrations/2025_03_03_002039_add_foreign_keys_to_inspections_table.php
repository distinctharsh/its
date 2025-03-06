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
        Schema::table('inspections', function (Blueprint $table) {
            $table->foreign(['category_id'])->references(['id'])->on('inspection_categories')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['category_type_id'])->references(['id'])->on('inspection_category_types')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['inspector_id'])->references(['id'])->on('inspectors')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['objection_department_id'])->references(['id'])->on('departments')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->dropForeign('inspections_category_id_foreign');
            $table->dropForeign('inspections_category_type_id_foreign');
            $table->dropForeign('inspections_created_by_foreign');
            $table->dropForeign('inspections_inspector_id_foreign');
            $table->dropForeign('inspections_objection_department_id_foreign');
        });
    }
};
