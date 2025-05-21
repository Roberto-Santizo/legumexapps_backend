<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeePermissionsController;
use App\Http\Controllers\LinesController;
use App\Http\Controllers\LineStockKeepingUnitsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SKUController;
use App\Http\Controllers\TaskProductionController;
use App\Http\Controllers\TaskProductionPlanNotesController;
use App\Http\Controllers\TimeOutController;
use App\Http\Controllers\WeeklyProductionPlanController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    //PRODUCCIÓN
    Route::get('/employees-comodines', [EmployeeController::class, 'getAllComodines']);

    Route::apiResource('/sku', SKUController::class);
    Route::get('/sku-all', [SKUController::class, 'GetAllSKU']);

    Route::apiResource('/lines', LinesController::class);
    Route::post('/lines/update-positions/{id}', [LinesController::class, 'UpdatePositions']);

    Route::get('/lines/performances-per-day/{id}', [LinesController::class, 'GetPerformanceByLine']);

    Route::get('/lines-all', [LinesController::class, 'GetAllLines']);
    Route::get('/lines-by-sku/{id}', [LinesController::class, 'GetAllLinesBySku']);

    Route::apiResource('/lines-skus', LineStockKeepingUnitsController::class);

    Route::apiResource('/timeouts', TimeOutController::class);
    Route::get('/timeouts-all', [TimeOutController::class, 'GetAllTimeouts']);

    Route::apiResource('/notes', TaskProductionPlanNotesController::class);

    Route::apiResource('/weekly_production_plan', WeeklyProductionPlanController::class);
    Route::get('/weekly_production_plan-all', [WeeklyProductionPlanController::class, 'GetAllWeeklyPlans']);
    Route::get('/weekly_production_plan/details/{weekly_plan_id}/{line_id}', [WeeklyProductionPlanController::class, 'GetTasksByLineId']);

    Route::get('/weekly_production_plan/tasks-no-operation-date/{weekly_plan_id}', [WeeklyProductionPlanController::class, 'GetTasksNoOperationDate']);
    Route::get('/weekly_production_plan/tasks/programed', [WeeklyProductionPlanController::class, 'GetTasksOperationDate']);

    Route::post('/weekly_production_plan/assign/{id}', [WeeklyProductionPlanController::class, 'createAssigments']);

    Route::apiResource('/task_production_plan', TaskProductionController::class);
    Route::get('/tasks_production_plan/finished/details/{id}', [TaskProductionController::class, 'FinishedTaskDetails']);
    Route::get('/tasks_production_plan/details/{id}', [TaskProductionController::class, 'TaskDetails']);
    Route::post('/tasks_production_plan/new-task', [TaskProductionController::class, 'CreateNewTaskProduction']);
    Route::post('/tasks_production_plan/create-assignee/{id}', [TaskProductionController::class, 'CreateAssignee']);
    Route::post('/tasks_production_plan/{id}/add-timeout/open', [TaskProductionController::class, 'AddTimeOutOpen']);
    Route::post('/tasks_production_plan/{id}/add-timeout/close', [TaskProductionController::class, 'AddTimeOutClose']);
    Route::post('/tasks_production_plan/{id}/assign', [TaskProductionController::class, 'Assign']);
    Route::post('/tasks_production_plan/change-assignment', [TaskProductionController::class, 'ChangeAssignment']);
    Route::post('/tasks_production_plan/{id}/performance', [TaskProductionController::class, 'TakePerformance']);
    Route::post('/tasks_production_plan/{id}/unassign', [TaskProductionController::class, 'Unassign']);


    Route::put('/tasks_production_plan/change-priority', [TaskProductionController::class, 'ChangePriority']);
    Route::patch('/tasks_production_plan/{id}/start', [TaskProductionController::class, 'StartTaskProduction']);
    Route::patch('/tasks_production_plan/change-operation-date/{id}', [TaskProductionController::class, 'ChangeOperationDate']);
    Route::patch('/tasks_production_plan/assign-operation-date/{id}', [TaskProductionController::class, 'AssignOperationDate']);
    Route::patch('/tasks_production_plan/{id}/end', [TaskProductionController::class, 'EndTaskProduction']);

    Route::apiResource('/employee-permissions', EmployeePermissionsController::class);

    Route::post('/report-production/{weekly_production_plan}/{line_id}', [ReportController::class, 'PlanillaProduccion']);
});
