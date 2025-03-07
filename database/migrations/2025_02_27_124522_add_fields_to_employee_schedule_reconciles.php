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
            // Add new columns if they don't exist
            if (!Schema::hasColumn('employee_schedule_reconciles', 'employee_id')) {
                $table->integer('employee_id')->unsigned()->index();
            }
            if (!Schema::hasColumn('employee_schedule_reconciles', 'reconcile_date')) {
                $table->date('reconcile_date');
            }
            if (!Schema::hasColumn('employee_schedule_reconciles', 'total_minutes')) {
                $table->integer('total_minutes');
            }
            if (!Schema::hasColumn('employee_schedule_reconciles', 'status')) {
                $table->string('status')->default('pending');
            }
            if (!Schema::hasColumn('employee_schedule_reconciles', 'approved_by_user_id')) {
                $table->integer('approved_by_user_id')->nullable()->unsigned()->index();
            }
            if (!Schema::hasColumn('employee_schedule_reconciles', 'approved_at')) {
                $table->dateTime('approved_at')->nullable();
            }
            if (!Schema::hasColumn('employee_schedule_reconciles', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_schedule_reconciles', function (Blueprint $table) {

            // We don't drop employee_schedule_reconciles as it may have existed before
            // But remove columns we added
            if (Schema::hasTable('employee_schedule_reconciles')) {
                Schema::table('employee_schedule_reconciles', function (Blueprint $table) {
                    $columns = [
                        'employee_id', 'reconcile_date', 'total_minutes',
                        'status', 'approved_by_user_id', 'approved_at', 'notes'
                    ];

                    foreach ($columns as $column) {
                        if (Schema::hasColumn('employee_schedule_reconciles', $column)) {
                            $table->dropColumn($column);
                        }
                    }
                });
            }
        });
    }
};
