<?php

namespace App\Console\Commands;

use App\Imports\AbilitiesImport;
use App\Imports\CountyImport;
use App\Imports\EmployeeImport;
use App\Imports\EthnicityImport;
use App\Imports\LanguageImport;
use App\Imports\LearningActivityTypeImport;
use App\Imports\LocationsImport;
use App\Imports\MaritalStatusImport;
use App\Imports\ModulesImport;
use App\Imports\ScheduleStatusImport;
use App\Imports\TeachersImport;
use App\Imports\ThemeImport;
use App\Imports\MilitaryRanksImport;
use App\Imports\SanctionsImport;
use App\Imports\SchoolingPeriodImport;
use App\Imports\StudentClassImport;
use App\Imports\StudentsImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class ImportExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all xlsx files';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        Schema::disableForeignKeyConstraints();

        DB::table('users')->truncate();
        DB::table('modules')->truncate();
        DB::table('abilities')->truncate();
        DB::table('employees')->truncate();
        DB::table('learning_activity_types')->truncate();
        DB::table('locations')->truncate();
        DB::table('teachers')->truncate();
        DB::table('themes')->truncate();
        DB::table('schedule_statuses')->truncate();
        DB::table('military_ranks')->truncate();
        DB::table('student_classes')->truncate();
        DB::table('schooling_periods')->truncate();
        DB::table('students')->truncate();
        DB::table('counties')->truncate();
        DB::table('ethnicities')->truncate();
        DB::table('languages')->truncate();
        DB::table('marital_statuses')->truncate();

        Schema::enableForeignKeyConstraints();

        $this->info('ModulesImport in progress...');
        Excel::import(new ModulesImport, storage_path('imports/modules.xlsx'));

        $this->info('AbilitiesImport in progress...');
        Excel::import(new AbilitiesImport, storage_path('imports/abilities.xlsx'));

        $this->info('EmployeeImport in progress...');
        Excel::import(new EmployeeImport, storage_path('imports/employees.xlsx'));

        $this->info('LearningActivityTypeImport in progress...');
        Excel::import(new LearningActivityTypeImport, storage_path('imports/learning_activity_type.xlsx'));

        $this->info('LocationsImport in progress...');
        Excel::import(new LocationsImport, storage_path('imports/locations.xlsx'));

        $this->info('TeachersImport in progress...');
        Excel::import(new TeachersImport, storage_path('imports/teachers.xlsx'));

        $this->info('ThemeImport in progress...');
        Excel::import(new ThemeImport, storage_path('imports/themes.xlsx'));

        $this->info('ScheduleStatusImport in progress...');
        Excel::import(new ScheduleStatusImport, storage_path('imports/schedule_statuses.xlsx'));

        $this->info('MilitaryRanksImport in progress...');
        Excel::import(new MilitaryRanksImport, storage_path('imports/military_ranks.xlsx'));

        $this->info('StudentClassImport in progress...');
        Excel::import(new StudentClassImport, storage_path('imports/student_classes.xlsx'));

        $this->info('SchoolingPeriodImport in progress...');
        Excel::import(new SchoolingPeriodImport, storage_path('imports/schooling_periods.xlsx'));

        $this->info('CountyImport in progress...');
        Excel::import(new CountyImport, storage_path('imports/counties.xlsx'));

        $this->info('EthnicityImport in progress...');
        Excel::import(new EthnicityImport, storage_path('imports/ethnicities.xlsx'));

        $this->info('LanguageImport in progress...');
        Excel::import(new LanguageImport, storage_path('imports/languages.xlsx'));

        $this->info('MaritalStatusImport in progress...');
        Excel::import(new MaritalStatusImport, storage_path('imports/marital_statuses.xlsx'));

        $this->info('MaritalStatusImport in progress...');
        Excel::import(new SanctionsImport, storage_path('imports/sanctions.xlsx'));

        $this->info('StudentsImport in progress...');
        Excel::import(new StudentsImport, storage_path('imports/students.xlsx'));
    }
}
