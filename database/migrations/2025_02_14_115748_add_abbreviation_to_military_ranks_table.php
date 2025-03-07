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
        Schema::table('military_ranks', function (Blueprint $table) {
            // Add abbreviation column after name column
            $table->string('abbreviation')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('military_ranks', function (Blueprint $table) {
            // Remove the column if we need to rollback
            $table->dropColumn('abbreviation');
        });
    }
};
