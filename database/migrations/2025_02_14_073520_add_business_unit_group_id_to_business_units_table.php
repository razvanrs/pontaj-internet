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
        Schema::table('business_units', function (Blueprint $table) {
            // Add the new column with default value of 1, without foreign key constraint
            $table->unsignedBigInteger('business_unit_group_id')
                  ->default(1)
                  ->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_units', function (Blueprint $table) {
            $table->dropColumn('business_unit_group_id');
        });
    }
};