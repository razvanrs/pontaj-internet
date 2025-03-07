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
        Schema::create('student_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers');

            $table->string('name');
            $table->integer('sel_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Define erp foreign keys
            $table->unsignedInteger('erp_id');
            $table->unsignedInteger('erp_schooling_period_id');
            $table->unsignedInteger('erp_form_teacher_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_classes');
    }
};
