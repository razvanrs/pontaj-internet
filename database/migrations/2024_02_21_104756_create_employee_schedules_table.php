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
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->integer('year_id')->index()->unsigned();
            $table->integer('week_id')->index()->unsigned();
            $table->integer('day_id')->index()->unsigned();
            $table->integer('employee_id')->index()->unsigned();
            $table->integer('schedule_status_id')->index()->unsigned();
            $table->dateTime('date_start', $precision = 0);
            $table->dateTime('date_finish', $precision = 0);
            $table->integer('total_minutes')->default(0);
            $table->integer('total_hours')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_schedules');
    }
};
