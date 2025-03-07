<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('schedule_statuses', function (Blueprint $table) {
            $table->dropColumn('background');
        });
    }

    public function down()
    {
        Schema::table('schedule_statuses', function (Blueprint $table) {
            $table->string('background')->nullable(); // Add the column back in case of rollback
        });
    }
};
