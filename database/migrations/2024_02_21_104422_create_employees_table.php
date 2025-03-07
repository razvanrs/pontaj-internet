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

        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Foreign key to users table
            $table->foreignId('user_id')->constrained();

            // Employee details
            $table->string('military_rank', 255);
            $table->foreignId('military_rank_type_id')->constrained();
            $table->string('name', 255);
            $table->string('surname', 255);
            $table->string('full_name', 255);
            $table->char('social_number', 13)->unique();
            $table->date('birthday');
            $table->foreignId('sex_id')->constrained('sexes');
            $table->string('father_surname', 255);
            $table->string('address', 255)->nullable();

            // Store phone numbers in a JSON column
            $table->json('phone_numbers');

            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();

            // ERP foreign key
            $table->unsignedBigInteger('erp_id')->index();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
