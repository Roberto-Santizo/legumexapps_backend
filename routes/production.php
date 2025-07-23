<?php

use App\Http\Controllers\DashboardProductionController;
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

Route::middleware('jwt.auth')->group(function () {

    Route::get('/employees-comodines', [EmployeeController::class, 'getComodines']);

    Route::apiResource('/lines-skus', LineStockKeepingUnitsController::class);
    Route::post('/lines-skus/upload', [LineStockKeepingUnitsController::class, 'UploadLinesSkus']);

    Route::apiResource('/timeouts', TimeOutController::class);
    Route::apiResource('/notes', TaskProductionPlanNotesController::class);

    Route::apiResource('/skus', SKUController::class);
    Route::post('/skus/upload', [SKUController::class, 'UploadStockKeepingUnits']);
    Route::post('/skus/upload/recipe', [SKUController::class, 'UploadSkuRecipe']);

    Route::apiResource('/lines', LinesController::class);
    Route::get('/lines/performances-per-day/{id}', [LinesController::class, 'GetPerformanceByLine']);
    Route::get('/lines/hours-per-week/{weekly_plan_id}', [LinesController::class, 'GetHoursPerWeek']);
    Route::get('/lines-by-sku/{id}', [LinesController::class, 'GetAllLinesBySku']);
    Route::post('/lines/update-positions/{id}', [LinesController::class, 'UpdatePositions']);

    Route::apiResource('/weekly-production-plans', WeeklyProductionPlanController::class);
    Route::get('/weekly-production-plans/details/{weekly_plan_id}/{line_id}', [WeeklyProductionPlanController::class, 'GetTasksByLineId']);
    Route::get('/weekly-production-plans/tasks-no-operation-date/{weekly_plan_id}', [WeeklyProductionPlanController::class, 'GetTasksNoOperationDate']);
    Route::get('/weekly-production-plans/events-for-calendar/{weekly_plan_id}', [WeeklyProductionPlanController::class, 'GetTasksForCalendar']);
    Route::get('/weekly-production-plans/tasks/programed/{weekly_plan_id}', [WeeklyProductionPlanController::class, 'GetTasksOperationDate']);
    Route::post('/weekly-production-plans/assign/{weekly_plan_id}', [WeeklyProductionPlanController::class, 'createAssigments']);
    Route::post('/weekly-production-plans/packing-material-necessity/{weekly_plan_id}', [ReportController::class, 'downloadPackingMaterialNecessity']);
    Route::post('/weekly-production-plans/report-weekly-production/{weekly_plan_id}', [ReportController::class, 'downloadWeeklyProduction']);

    Route::apiResource('/tasks-production', TaskProductionController::class);
    Route::get('/tasks-production/reprogram-details/{id}', [TaskProductionController::class, 'TaskReprogramDetails']);
    Route::get('/tasks-production/finished/details/{id}', [TaskProductionController::class, 'FinishedTaskDetails']);
    Route::get('/tasks-production/details/{id}', [TaskProductionController::class, 'TaskDetails']);
    Route::get('/tasks-production/devolution-details/{id}', [TaskProductionController::class, 'TaskDevolutionDetails']);
    Route::get('/tasks-production/active-employees/{id}', [TaskProductionController::class, 'TaskActiveEmployees']);
    Route::post('/tasks-production/new-task/{weekly_plan_id}', [TaskProductionController::class, 'CreateNewTaskProduction']);
    Route::post('/tasks-production/create-assignees/{id}', [TaskProductionController::class, 'CreateAssignee']);
    Route::post('/tasks-production/{id}/add-timeout/open', [TaskProductionController::class, 'AddTimeOutOpen']);
    Route::post('/tasks-production/{id}/add-timeout/close', [TaskProductionController::class, 'AddTimeOutClose']);
    Route::post('/tasks-production/{id}/assign', [TaskProductionController::class, 'Assign']);
    Route::post('/tasks-production/{id}/performance', [TaskProductionController::class, 'TakePerformance']);
    Route::post('/tasks-production/{id}/unassign', [TaskProductionController::class, 'Unassign']);
    Route::put('/tasks-production/change-priority', [TaskProductionController::class, 'ChangePriority']);
    Route::patch('/tasks-production/{id}/confirm-assignments', [TaskProductionController::class, 'ConfirmAssignments']);
    Route::patch('/tasks-production/{id}/start', [TaskProductionController::class, 'StartTaskProduction']);
    Route::patch('/tasks-production/change-operation-date/{id}', [TaskProductionController::class, 'ChangeOperationDate']);
    Route::patch('/tasks-production/assign-operation-date/{id}', [TaskProductionController::class, 'AssignOperationDate']);
    Route::patch('/tasks-production/{id}/end', [TaskProductionController::class, 'EndTaskProduction']);
    Route::patch('/tasks-production/{id}/unassign', [TaskProductionController::class, 'UnassignTaskProduction']);

    Route::apiResource('/employee-permissions', EmployeePermissionsController::class);

    //DASHBOARD
    Route::get('/dashboard/production/finished-tasks-per-line', [DashboardProductionController::class, 'GetFinishedTasksPerLine']);
    Route::get('/dashboard/production/in-progress', [DashboardProductionController::class, 'GetInProgressTasks']);
    // Route::get('/dashboard/production/finished-tasks', [DashboardProductionController::class, 'GetInProgressTasks']);
});
