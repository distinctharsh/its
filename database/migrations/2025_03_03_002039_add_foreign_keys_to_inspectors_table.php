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
        Schema::table('inspectors', function (Blueprint $table) {
            $table->foreign(['ib_status_id'], 'fk_ib_status_id')->references(['id'])->on('statuses')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['mea_status_id'], 'fk_mea_status_id')->references(['id'])->on('statuses')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['raw_status_id'], 'fk_raw_status_id')->references(['id'])->on('statuses')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['designation_id'])->references(['id'])->on('designations')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['gender_id'])->references(['id'])->on('genders')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['nationality_id'])->references(['id'])->on('nationalities')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['rank_id'])->references(['id'])->on('ranks')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspectors', function (Blueprint $table) {
            $table->dropForeign('fk_ib_status_id');
            $table->dropForeign('fk_mea_status_id');
            $table->dropForeign('fk_raw_status_id');
            $table->dropForeign('inspectors_designation_id_foreign');
            $table->dropForeign('inspectors_gender_id_foreign');
            $table->dropForeign('inspectors_nationality_id_foreign');
            $table->dropForeign('inspectors_rank_id_foreign');
        });
    }
};
