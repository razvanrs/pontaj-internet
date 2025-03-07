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
        // Create extra_hours table
        Schema::create('extra_hours', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->unsigned()->index();
            $table->integer('employee_schedule_id')->unsigned()->index();
            $table->unsignedBigInteger('business_unit_id')->nullable()->index();
            $table->date('date');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('total_minutes');
            $table->integer('remaining_minutes');
            $table->string('status'); // available, partially_reconciled, reconciled, expired
            $table->date('expiry_date');
            $table->boolean('is_fully_reconciled')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_hours');
    }
};
