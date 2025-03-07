<?php

namespace App\Console\Commands;

use App\Imports\BusinessUnitEmployeeImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class ImportBusinessUnitDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-business-unit-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import business units and their employee relations from the employee Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Schema::disableForeignKeyConstraints();

        // Clear existing data
        DB::table('business_units')->truncate();
        DB::table('business_unit_employees')->truncate();

        Schema::enableForeignKeyConstraints();

        $this->info('Importing business units and employee relations...');
        Excel::import(
            new BusinessUnitEmployeeImport,
            storage_path('imports/employees_import_business_unit.xlsx')
        );

        $this->info('Import completed successfully!');

        // Display summary
        $this->info('Summary:');
        $this->info('Business Units created: ' . DB::table('business_units')->count());
        $this->info('Employee relations created: ' . DB::table('business_unit_employees')->count());
    }
}
