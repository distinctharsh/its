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
        Schema::table('other_staff', function (Blueprint $table) {
            $table->foreign(['gender_id'], 'other_staff_ibfk_1')->references(['id'])->on('genders')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['nationality_id'], 'other_staff_ibfk_2')->references(['id'])->on('nationalities')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['designation_id'], 'other_staff_ibfk_3')->references(['id'])->on('designations')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['rank_id'], 'other_staff_ibfk_4')->references(['id'])->on('ranks')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['opcw_document_id'], 'other_staff_ibfk_5')->references(['id'])->on('opcw_faxes')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('other_staff', function (Blueprint $table) {
            $table->dropForeign('other_staff_ibfk_1');
            $table->dropForeign('other_staff_ibfk_2');
            $table->dropForeign('other_staff_ibfk_3');
            $table->dropForeign('other_staff_ibfk_4');
            $table->dropForeign('other_staff_ibfk_5');
        });
    }
};
