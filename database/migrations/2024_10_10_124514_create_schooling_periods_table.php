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
        Schema::create('schooling_periods', function (Blueprint $table) {
            $table->id();
            $table->string('short_description');
            $table->date('started_at');
            $table->date('finished_at');
            $table->smallInteger('sel_order');
            $table->timestamps();

            // Define erp foreign keys
            $table->unsignedInteger('erp_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schooling_periods');
    }
};
