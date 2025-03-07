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
        Schema::create('military_rank_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // personal contractual, subofiter, ofiter
            $table->smallInteger('sel_order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('military_rank_types');
    }
};
