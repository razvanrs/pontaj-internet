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
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('domicile_county_id')->nullable()->change();
            $table->dropForeign('students_domicile_county_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('domicile_county_id')->nullable(false)->change();
            $table->foreign('domicile_county_id')->references('id')->on('counties');
        });
    }
};
