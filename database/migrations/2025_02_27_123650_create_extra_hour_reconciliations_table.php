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
         // Create extra_hour_reconciliations table
         Schema::create('extra_hour_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->unsigned()->index();
            $table->integer('extra_hour_id')->unsigned()->index();
            $table->integer('business_unit_id')->nullable()->unsigned()->index();
            $table->integer('employee_schedule_reconcile_id')->nullable()->unsigned()->index();
            $table->date('reconciliation_date');
            $table->integer('minutes_reconciled');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('notes')->nullable();
            $table->integer('approved_by_user_id')->nullable()->unsigned()->index();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_hour_reconciliations');
    }
};
