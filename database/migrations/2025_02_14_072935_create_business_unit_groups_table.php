<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_unit_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code')->index(); // Unique code for the group
            $table->string('name'); // Name of the group
            $table->integer('sel_order')->default(0); // Order for display purposes
            $table->text('description')->nullable(); // Optional description
            $table->timestamps();
            $table->softDeletes(); // Add soft deletes
        });
    }

    public function down(): void
    {        
        Schema::dropIfExists('business_unit_groups');
    }
};