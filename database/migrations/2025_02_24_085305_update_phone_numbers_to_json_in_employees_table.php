<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // First, create a temporary backup of the original column
        Schema::table('employees', function (Blueprint $table) {
            $table->text('phone_numbers_backup')->nullable();
        });

        // Backup the data
        DB::statement('UPDATE employees SET phone_numbers_backup = phone_numbers');

        // Change the column type to JSON
        // Note: MySQL will automatically cast the existing JSON-formatted string to JSON
        Schema::table('employees', function (Blueprint $table) {
            $table->json('phone_numbers')->change();
        });

        // Remove the backup column after confirming the migration worked
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('phone_numbers_backup');
        });
    }

    public function down()
    {
        // Convert back to text if needed
        Schema::table('employees', function (Blueprint $table) {
            $table->text('phone_numbers')->change();
        });
    }
};
