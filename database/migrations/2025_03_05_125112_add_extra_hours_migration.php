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
        // Ensure we have all required fields in the extra_hours table
        if (Schema::hasTable('extra_hours') && !Schema::hasColumn('extra_hours', 'segment_number')) {
            Schema::table('extra_hours', function (Blueprint $table) {
                // Add segment number for multi-part shifts
                $table->unsignedSmallInteger('segment_number')->nullable()->after('employee_schedule_id');

                // Add description field for better context
                $table->string('description')->nullable()->after('business_unit_id');

                // Add index for segment grouping
                $table->index(['employee_schedule_id', 'segment_number']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('extra_hours') && Schema::hasColumn('extra_hours', 'segment_number')) {
            Schema::table('extra_hours', function (Blueprint $table) {
                $table->dropColumn('segment_number');
                $table->dropColumn('description');

                // Drop the index
                $table->dropIndex(['employee_schedule_id', 'segment_number']);
            });
        }
    }
};
