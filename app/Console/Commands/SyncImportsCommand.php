<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use App\Imports\EmployeeSyncImport;
use App\Imports\SchoolingPeriodSyncImport;
use App\Imports\StudentClassSyncImport;
use App\Imports\StudentsSyncImport;

class SyncImportsCommand extends Command
{
    protected $signature = 'app:sync-imports';
    protected $description = 'Synchronize data from Excel imports';
    private $employeeImporter = null;
    private $studentClassImporter = null;
    private $studentImporter = null;

    public function handle()
    {
        Schema::disableForeignKeyConstraints();

        $this->info('Synchronising...');

        $this->info('SchoolingPeriodImport in progress...');
        Excel::import(new SchoolingPeriodSyncImport, storage_path('imports/schooling_periods.xlsx'));

        $this->info('EmployeeImport in progress...');
        $this->employeeImporter = $this->syncEmployees();

        $this->info('StudentClassImport in progress...');
        $this->studentClassImporter = $this->syncStudentClasses();

        $this->info('StudentsImport in progress...');
        $this->studentImporter = $this->syncStudents();

        $this->info('Synchronising completed!');

        Schema::enableForeignKeyConstraints();

        $this->displaySummary();

        $this->info('All imports have been successfully processed.');
    }

    public function syncEmployees()
    {
        try {
            $importer = new EmployeeSyncImport();
            Excel::import($importer, storage_path('imports/employees.xlsx'));
            $this->info("Employee sync completed successfully");
            return $importer;
        } catch (\Exception $e) {
            $this->error("Error syncing employees: " . $e->getMessage());
            return null;
        }
    }

    public function syncStudentClasses()
    {
        try {
            $importer = new StudentClassSyncImport();
            Excel::import($importer, storage_path('imports/student_classes.xlsx'));
            $this->info("Student class sync completed successfully");
            return $importer;
        } catch (\Exception $e) {
            $this->error("Error syncing student classes: " . $e->getMessage());
            return null;
        }
    }

    public function syncStudents()
    {
        try {
            $importer = new StudentsSyncImport();
            Excel::import($importer, storage_path('imports/students.xlsx'));
            $this->info("Student sync completed successfully");
            return $importer;
        } catch (\Exception $e) {
            $this->error("Error syncing students: " . $e->getMessage());
            return null;
        }
    }

    public function displaySummary()
    {
        // Employee summary
        if ($this->employeeImporter) {
            $stats = $this->employeeImporter->getImportStats();
            $this->info('Import Summary:');
            $this->info("- {$stats['created']} employees created");
            $this->info("- {$stats['updated']} employees updated");

            if ($stats['created'] > 0) {
                $this->info("\nNew employees:");
                $this->table(
                    ['ID', 'ERP ID', 'Name', 'Email'],
                    collect($stats['newEmployees'])->map(function ($employee) {
                        return [
                            $employee['id'],
                            $employee['erp_id'],
                            $employee['full_name'],
                            $employee['email']
                        ];
                    })->toArray()
                );
            }
        }

        // Student Class summary
        if ($this->studentClassImporter) {
            $stats = $this->studentClassImporter->getImportStats();
            $this->info("\nStudent Classes Import Summary:");
            $this->info("- {$stats['created']} classes created");
            $this->info("- {$stats['updated']} classes updated");

            if ($stats['created'] > 0) {
                $this->info("\nNew classes:");
                $this->table(
                    ['ID', 'ERP ID', 'Name'],
                    collect($stats['newClasses'])->map(function ($class) {
                        return [
                            $class['id'],
                            $class['erp_id'],
                            $class['name']
                        ];
                    })->toArray()
                );
            }
        }

        // Student summary
        if ($this->studentImporter) {
            $stats = $this->studentImporter->getImportStats();
            $this->info("\nStudents Import Summary:");
            $this->info("- {$stats['created']} students created");
            $this->info("- {$stats['updated']} students updated");

            if ($stats['created'] > 0) {
                $this->info("\nNew students:");
                $this->table(
                    ['ID', 'ERP ID', 'Name', 'Email', 'Class'],
                    collect($stats['newStudents'])->map(function ($student) {
                        return [
                            $student['id'],
                            $student['erp_id'],
                            $student['full_name'],
                            $student['email'],
                            $student['class_name'] ?? '-'
                        ];
                    })->toArray()
                );
            }
        }
    }
}
