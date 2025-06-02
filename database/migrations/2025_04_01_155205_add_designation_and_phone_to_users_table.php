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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('designation_id')->nullable(); // Removed 'after('some_column')'

            // Add phone_number column
            $table->string('phone_number')->nullable(); // You can change 15 to a preferred length if necessary
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('designation_id');
            $table->dropColumn('phone_number');
        });
    }
};
