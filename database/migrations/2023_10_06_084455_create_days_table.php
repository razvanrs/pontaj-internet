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
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->integer('year_id')->index()->unsigned();
            $table->integer('week_id')->index()->unsigned();
            $table->string('code');
            $table->integer('value');
            $table->string('lokalize_long_name');
            $table->string('lokalize_short_name');
            $table->integer('day_of_year');
            $table->integer('day_of_week');
            $table->integer('month_id')->unsigned()->index();
            $table->string('string_representation')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
