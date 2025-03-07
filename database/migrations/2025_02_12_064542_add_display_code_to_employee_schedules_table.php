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
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->string('display_code')->nullable()->after('total_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->dropColumn('display_code');
        });
    }
};