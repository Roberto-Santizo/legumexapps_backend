<?php

use App\Http\Controllers\CDPController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\DashboardAgricolaController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FincaController;
use App\Http\Controllers\InsumosController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\TaskCropController;
use App\Http\Controllers\TasksCropController;
use App\Http\Controllers\TasksLoteController;
use App\Http\Controllers\WeeklyPlanController;
use Illuminate\Support\Facades\Route;

Route::middleware('jwt.auth')->group(function () {
    Route::get('/employees/{id}/{taskId}', [EmployeeController::class, 'index']);

    Route::get('/recipes', [RecipeController::class, 'index']);
    Route::get('/crops', [CropController::class, 'index']);

    Route::apiResource('/fincas', FincaController::class);

    Route::apiResource('/tareas', TareaController::class);
    Route::post('/tareas/upload', [TareaController::class, 'UploadTasks']);

    Route::apiResource('/cdps', CDPController::class);
    Route::post('/cdps/upload', [CDPController::class, 'UploadCDPS']);

    Route::apiResource('/tasks-crop', TaskCropController::class);

    Route::apiResource('/lotes', LoteController::class);
    Route::post('/lotes-all/update', [LoteController::class, 'UpdateLotes']);

    Route::apiResource('/plans', WeeklyPlanController::class);
    Route::get('/plans/tasks-no-planification-date/{id}', [WeeklyPlanController::class, 'GetTasksWithNoPlanificationDate']);
    Route::get('/plans/tasks-for-calendar/{id}', [WeeklyPlanController::class, 'GetTasksForCalendar']);
    Route::get('/plans/tasks-planned-by-date/finca', [WeeklyPlanController::class, 'GetTasksPlannedByDate']);

    Route::apiResource('/tasks-lotes', TasksLoteController::class);
    Route::get('/tasks-lotes/edit/{id}', [TasksLoteController::class, 'GetTaskForEdit']);
    Route::get('/tasks-lotes/{id}/details', [TasksLoteController::class, 'TaskDetail']);
    Route::post('/tasks-lotes/register-insumos', [TasksLoteController::class, 'RegisterInsumos']);
    Route::post('/tasks-lotes/close-assignment/{id}', [TasksLoteController::class, 'CloseAssigment']);
    Route::patch('/tasks-lotes/close/{id}', [TasksLoteController::class, 'CloseTask']);
    Route::patch('/tasks-lotes/change-operation-date/update', [TasksLoteController::class, 'ChangeOperationDate']);
    Route::patch('/tasks-lotes/partial-close/close/{id}', [TasksLoteController::class, 'PartialClose']);
    Route::patch('/tasks-lotes/partial-close/open/{id}', [TasksLoteController::class, 'PartialCloseOpen']);
    Route::patch('/tasks-lotes/prepared-insumos/{id}', [TasksLoteController::class, 'PreparedInsumos']);
    Route::delete('/tasks-lotes/erase/{id}', [TasksLoteController::class, 'EraseAssignationTask']);

    Route::apiResource('/tasks-crops-lotes', TasksCropController::class);
    Route::get('/tasks-crops-lotes/employees/{id}', [TasksCropController::class, 'EmployeesAssignment']);
    Route::get('/tasks-crops-lotes/daily-employees/{id}', [TasksCropController::class, 'EmployeesAssignment']);
    Route::get('/tasks-crops-lotes/details/{id}', [TasksCropController::class, 'TaskCropDetail']);
    Route::get('/tasks-crops-lotes/incomplete-assigments/{id}', [TasksCropController::class, 'GetIncompleteAssignments']);
    Route::get('/tasks-crops-lotes/daily-employees/{id}', [TasksCropController::class, 'GetAssignedEmployees']);
    Route::post('/tasks-crops-lotes/close-assignment/{id}', [TasksCropController::class, 'CloseAssigment']);
    Route::post('/tasks-crops-lotes/close-daily-assigment/{id}', [TasksCropController::class, 'CloseDailyAssigment']);
    Route::post('/tasks-crops-lotes/register-daily-assigment', [TasksCropController::class, 'RegisterDailyAssigment']);

    Route::apiResource('/insumos', InsumosController::class);
    Route::post('/insumos/upload', [InsumosController::class, 'UploadInsumos']);

    Route::get('/dashboard/agricola/dron-hours', [DashboardAgricolaController::class, 'GetDronHours']);
    Route::get('/dashboard/agricola/summary-hours-employees', [DashboardAgricolaController::class, 'GetSummaryHoursEmployees']);
    Route::get('/dashboard/agricola/tasks-in-progress', [DashboardAgricolaController::class, 'GetTasksInProgress']);
    Route::get('/dashboard/agricola/tasks-crops-in-progress', [DashboardAgricolaController::class, 'GetTasksCropInProgress']);
    Route::get('/dashboard/agricola/finished-tasks', [DashboardAgricolaController::class, 'GetFinishedTasks']);
    Route::get('/dashboard/agricola/finished-tasks-crop', [DashboardAgricolaController::class, 'GetFinishedTasksCrop']);
    Route::get('/dashboard/agricola/finished-total-tasks-finca', [DashboardAgricolaController::class, 'GetFinishedTasksByFinca']);
});
