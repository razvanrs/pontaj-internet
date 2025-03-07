<?php

use Illuminate\Database\Migrations\Migration; 
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add new column - just indexed, no constraint
            $table->unsignedInteger('military_rank_id')->nullable()->after('military_rank')->index();
        });

        // Transfer existing data
        DB::table('employees')->orderBy('id')->chunk(100, function ($employees) {
            foreach ($employees as $employee) {
                // Find or create military rank
                $militaryRank = DB::table('military_ranks')
                    ->where('name', $employee->military_rank)
                    ->first();

                if ($militaryRank) {
                    DB::table('employees')
                        ->where('id', $employee->id)
                        ->update(['military_rank_id' => $militaryRank->id]);
                }
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            // Remove old column
            $table->dropColumn('military_rank');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Recreate old column
            $table->string('military_rank')->after('user_id')->nullable();
            
            // Transfer data back
            DB::table('employees')->orderBy('id')->chunk(100, function ($employees) {
                foreach ($employees as $employee) {
                    $militaryRank = DB::table('military_ranks')
                        ->where('id', $employee->military_rank_id)
                        ->first();

                    if ($militaryRank) {
                        DB::table('employees')
                            ->where('id', $employee->id)
                            ->update(['military_rank' => $militaryRank->name]);
                    }
                }
            });

            // Remove new column
            $table->dropColumn('military_rank_id');
        });
    }
};