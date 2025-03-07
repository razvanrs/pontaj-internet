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
        Schema::create('temporary_observations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_unit_group_id')->nullable();
            $table->integer('year')->nullable();
            $table->integer('month')->nullable();
            $table->integer('person_index')->nullable();
            $table->text('observations')->nullable();
            $table->string('storage_key')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_observations');
    }
};