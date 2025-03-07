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
        Schema::table('employee_schedule_reconciles', function (Blueprint $table) {
            // Rename the misspelled column to the correct name
            $table->renameColumn('employee_shedule_id', 'employee_schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_schedule_reconciles', function (Blueprint $table) {
            // Revert the change if needed
            $table->renameColumn('employee_schedule_id', 'employee_shedule_id');
        });
    }
};
