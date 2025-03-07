<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManagementCalendarController;
use App\Http\Controllers\PlanificareProfesoriController;
use App\Http\Controllers\PlanificarePersonalController;
use App\Http\Controllers\CarnetAbateriEleviController;
use App\Http\Controllers\OreRecuperareController;
use App\Http\Controllers\Rapoarte\RaportSituatiePrezentaZilnicaController;
use App\Http\Controllers\Rapoarte\RaportSituatiePrezentaLunaraController;
use App\Http\Controllers\Rapoarte\RaportPlanificareProfesoriController;
use App\Http\Controllers\WorkingDaysController;
use App\Http\Controllers\TemporaryObservationController;
use App\Data\DayLimitData;
use App\Models\DayLimit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // MAIN
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/management-calendar', [ManagementCalendarController::class, 'index'])->name('management-calendar');
    Route::post('/adauga-eveniment', [ManagementCalendarController::class, 'addEvent'])->name('adauga-eveniment');
    Route::get('/ore-recuperare', [OreRecuperareController::class, 'index'])->name('ore-recuperare');
    Route::post('/adauga-ora-recuperare', [OreRecuperareController::class, 'addRecovery'])->name('adauga-ora-recuperare');

    // RAPOARTE
    Route::get('/raport-situatie-prezenta-zilnica', [RaportSituatiePrezentaZilnicaController::class, 'index'])->name('raport-situatie-prezenta-zilnica');
    Route::get('/rapoarte/prezenta-zilnica/data', [RaportSituatiePrezentaZilnicaController::class, 'getDailyData'])->name('rapoarte.prezenta-zilnica.data');
    Route::get('/raport-situatie-prezenta-lunara', [RaportSituatiePrezentaLunaraController::class, 'index'])->name('raport-situatie-prezenta-lunara');
    Route::get('/raport-planificare-profesori', [RaportPlanificareProfesoriController::class, 'index'])->name('raport-planificare-profesori');

    Route::middleware(['auth'])->group(function () {
        Route::get('/raport-situatie-prezenta-lunara', [RaportSituatiePrezentaLunaraController::class, 'index'])
            ->name('raport-situatie-prezenta-lunara');
        Route::get('/rapoarte/prezenta-lunara/data', [RaportSituatiePrezentaLunaraController::class, 'getMonthlyData'])
            ->name('rapoarte.prezenta-lunara.data');
    });

    // TEACHER SCHEDULE
    Route::get('/planificare-profesori', [PlanificareProfesoriController::class, 'index'])->name('teacherSchedule');
    Route::post('/adauga-activitate-profesor', [PlanificareProfesoriController::class, 'addTeacherActivity'])->name('frm.add.teacherSchedule');
    Route::post('/actualizeaza-activitate-profesor', [PlanificareProfesoriController::class, 'updateTeacherActivity'])->name('frm.update.teacherSchedule');
    Route::post('/sterge-activitate-profesor', [PlanificareProfesoriController::class, 'deleteTeacherActivity'])->name('frm.delete.teacherSchedule');
    Route::post('/teacher/events', [PlanificareProfesoriController::class, 'getEvents'])->name('teacher.getEvents');
    Route::post('/teacher/events/{id}', [PlanificareProfesoriController::class, 'getEvent'])->name('teacher.getEvent');

    // EMPLOYEE SCHEDULE
    Route::get('/planificare-personal', [PlanificarePersonalController::class, 'index'])->name('employeeSchedule');
    Route::post('/adauga-activitate-personal', [PlanificarePersonalController::class, 'addEmployeeActivity'])->name('frm.add.employeeSchedule');
    Route::post('/adauga-activitate-personal-bulk', [PlanificarePersonalController::class, 'addEmployeeActivityBulk']);
    Route::post('/actualizeaza-activitate-personal', [PlanificarePersonalController::class, 'updateEmployeeActivity'])->name('frm.update.employeeSchedule');
    Route::post('/sterge-activitate-personal', [PlanificarePersonalController::class, 'deleteEmployeeActivity'])->name('frm.delete.employeeSchedule');
    Route::post('/employees/by-business-unit-group', [PlanificarePersonalController::class, 'getEmployeesByBusinessUnitGroup'])->name('lists.employees');
    Route::post('/employee/events', [PlanificarePersonalController::class, 'getEvents'])->name('employee.getEvents');
    Route::post('/employee/events/{id}', [PlanificarePersonalController::class, 'getEvent'])->name('employee.getEvent');

    // CARNET ABATERI ELEVI
    Route::get('/carnet-abateri-elevi', [CarnetAbateriEleviController::class, 'index'])->name('sanction');
    Route::post('/carnet-abateri-elevi', [CarnetAbateriEleviController::class, 'store'])->name('sanction.store');
    Route::put('/carnet-abateri-elevi/{studentSanction}', [CarnetAbateriEleviController::class, 'update'])->name('sanction.update');
    Route::post('/carnet-abateri-elevi/editeaza', [CarnetAbateriEleviController::class, 'edit'])->name('sanction.edit');
    Route::delete('/carnet-abateri-elevi/{studentSanction}', [CarnetAbateriEleviController::class, 'destroy'])->name('sanction.destroy');
    Route::get('/students/search', [CarnetAbateriEleviController::class, 'search']);

    // CALENDAR EVENTS
    Route::post('/day-limits/range', [ManagementCalendarController::class, 'getEventsByDateRange'])->name('day-limits.range');
    Route::get('/working-days/{year}/{month}', [WorkingDaysController::class, 'calculate']);
    // ACTUALIZARE ZILE LIBERE CALENDAR
    Route::get('/update-calendar/{year}', [WorkingDaysController::class, 'fetchHolidays'])->name('update-calendar');


    // Temporary Observations Routes
    Route::post('/temporary-observations/store', [TemporaryObservationController::class, 'store'])->name('temp-observations.store');
    Route::get('/temporary-observations/by-month', [TemporaryObservationController::class, 'getByMonth'])->name('temp-observations.by-month');
    Route::delete('/temporary-observations/delete-all', [TemporaryObservationController::class, 'deleteAll'])->name('temp-observations.delete-all');

});