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
        Schema::disableForeignKeyConstraints();

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_class_id')->constrained('student_classes');
            $table->foreignId('birth_county_id')->nullable()->constrained('counties');
            $table->foreignId('domicile_county_id')->constrained('counties');
            $table->foreignId('selection_county_id')->constrained('counties');
            $table->foreignId('residence_county_id')->nullable()->constrained('counties');
            $table->foreignId('practice_county_id')->nullable()->constrained('counties');
            $table->foreignId('ethnicity_id')->constrained('ethnicities');
            $table->foreignId('sex_id')->constrained('sexes');
            $table->foreignId('language_id')->constrained('languages');
            $table->foreignId('marital_status_id')->constrained('marital_statuses');
            $table->foreignId('schooling_period_id')->constrained('schooling_periods');
            // TODO: de creat relatie many to many
            // $table->foreignId('remark_id')->nullable()->constrained('remarks');

            $table->string('full_name');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('father_first_name')->nullable();
            $table->string('mother_first_name')->nullable();
            $table->date('birthday');
            $table->string('birth_town');
            $table->string('domicile_town');
            $table->string('residence_town')->nullable();
            $table->string('practice_town')->nullable();
            $table->string('address')->nullable();
            $table->string('matriculation_number')->nullable();
            $table->string('identity_card_series')->nullable();
            $table->string('identity_card_number')->nullable();
            $table->string('admission_exam_code');
            $table->unsignedDecimal('admission_exam_score', 4, 2);
            $table->unsignedDecimal('bac_grades_average', 4, 2)->nullable();
            $table->unsignedDecimal('bac_romanian_language_grade', 4, 2)->nullable();
            $table->unsignedDecimal('bac_main_subject_profile_grade', 4, 2)->nullable();
            $table->unsignedDecimal('bac_subject_of_choice_profile_grade', 4, 2)->nullable();
            $table->unsignedDecimal('high_school_avg_grade_for_1st_foreign_lang', 4, 2)->nullable();
            $table->unsignedDecimal('high_school_avg_grade_for_2nd_foreign_lang', 4, 2)->nullable();
            $table->string('car_brand')->nullable();
            $table->string('car_registration_number')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Define erp foreign keys
            $table->unsignedInteger('erp_student_class_id');
            $table->unsignedInteger('erp_schooling_period_id');
            $table->unsignedInteger('erp_id')->unique();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
