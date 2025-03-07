<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExtraHoursApiController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Get available extra hours for an employee
    Route::get('/extra-hours/available', [ExtraHoursApiController::class, 'getAvailableExtraHours']);

    // Calculate extra hours for a specific schedule
    Route::post('/extra-hours/calculate', [ExtraHoursApiController::class, 'calculateExtraHours']);

    // Recalculate extra hours for an employee in a date range
    Route::post('/extra-hours/recalculate', [ExtraHoursApiController::class, 'recalculateExtraHours']);

    // Reconciliations
    Route::post('/reconciliations', [\App\Http\Controllers\Api\ReconciliationController::class, 'store']);
});
