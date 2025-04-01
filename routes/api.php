<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\CarriersController;
use App\Http\Controllers\CDPController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\DashboardAgricolaController;
use App\Http\Controllers\DashboardCalidad;
use App\Http\Controllers\DefectController;
use App\Http\Controllers\DriversController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeePermissionsController;
use App\Http\Controllers\FincaController;
use App\Http\Controllers\InspectorController;
use App\Http\Controllers\InsumosController;
use App\Http\Controllers\LinesController;
use App\Http\Controllers\LineStockKeepingUnitsController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PlantasController;
use App\Http\Controllers\PlatesController;
use App\Http\Controllers\ProducersController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductorPlantationControlController;
use App\Http\Controllers\ProductsSKUController;
use App\Http\Controllers\QualityStatusesController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RmReceptionsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SKUController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\TaskCropController;
use App\Http\Controllers\TaskProductionController;
use App\Http\Controllers\TaskProductionPlanNotesController;
use App\Http\Controllers\TasksCropController;
use App\Http\Controllers\TasksLoteController;
use App\Http\Controllers\TimeOutController;
use App\Http\Controllers\TransportConditionController;
use App\Http\Controllers\TransportInspectionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VarietyProductsController;
use App\Http\Controllers\WeeklyPlanController;
use App\Http\Controllers\WeeklyProductionPlanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    //AGRICOLA
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/users', UsersController::class);
    Route::get('/users-info/{user}/info', [UsersController::class,'UsersInfo']);
    Route::patch('/users/{user}/status', [UsersController::class, 'updateStatus']);
    Route::apiResource('/user',UserController::class);
    Route::get('/permissions/user',[PermissionController::class,'userPermissions']);
    Route::apiResource('/permissions',PermissionController::class);
    Route::get('/roles/user',[RoleController::class,'userRoles']);
    Route::apiResource('/roles',RoleController::class);
    Route::apiResource('/tareas', TareaController::class);
    Route::get('/tareas-all',[TareaController::class,'GetAllTareas']);
    Route::post('/tareas/upload', [TareaController::class,'UploadTasks']);
    Route::apiResource('/tasks-crop',TaskCropController::class);
    Route::get('/tasks-crop-all',[TaskCropController::class,'GetAllTasksCrop']);
    Route::apiResource('/cdps',CDPController::class);
    Route::get('/cdps-list/all',[CDPController::class,'GetAllCDPS']);
    Route::get('/cdps/lote/{lote}',[CDPController::class,'GetCDPSByLoteId']);
    Route::post('cdps/upload',[CDPController::class,'UploadCDPS']);
    Route::get('/cdp/info',[CDPController::class,'GetCDPInfo']);
    Route::apiResource('/lotes', LoteController::class);
    Route::get('/lotes-all',[LoteController::class,'GetAllLotes']);
    Route::get('/lotes/finca/{finca}', [LoteController::class,'GetLotesByFincaId']);
    Route::post('/lotes-all/update',[LoteController::class,'UpdateLotes']);
    Route::apiResource('/plans',WeeklyPlanController::class);
    Route::get('/plans-list/all',[WeeklyPlanController::class,'GetAllPlans']);
    Route::post('/report/plans',[ReportController::class,'DownloadReport']);
    Route::get('/report/insumos/{id}',[ReportController::class,'DownloadReportInsumos']);
    
    Route::apiResource('/recipes',RecipeController::class);
    Route::apiResource('/crops',CropController::class);
    Route::apiResource('/fincas',FincaController::class);
    Route::apiResource('/tasks-lotes',TasksLoteController::class);
    Route::get('/tasks-lotes/{id}/details',[TasksLoteController::class,'TaskDetail']);
    Route::post('/tasks-lotes/register-insumos',[TasksLoteController::class,'RegisterInsumos']);
    Route::post('/tasks-lotes/close-assignment/{id}', [TasksLoteController::class, 'CloseAssigment']);
    Route::patch('/tasks-lotes/close/{id}',[TasksLoteController::class, 'CloseTask']);
    Route::patch('/tasks-lotes/partial-close/close/{id}', [TasksLoteController::class, 'PartialClose']);
    Route::patch('/tasks-lotes/partial-close/open/{id}', [TasksLoteController::class, 'PartialCloseOpen']);
    Route::delete('/tasks-lotes/erase/{id}',[TasksLoteController::class, 'EraseAssignationTask']);
    Route::apiResource('/tasks-crops-lotes',TasksCropController::class);
    Route::get('/tasks-crops-lotes/employees/{id}', [TasksCropController::class, 'EmployeesAssignment']);
    Route::get('/tasks-crops-lotes/daily-employees/{id}', [TasksCropController::class, 'EmployeesAssignment']);
    Route::get('/tasks-crops-lotes/details/{id}', [TasksCropController::class, 'TaskCropDetail']);
    Route::get('/tasks-crops-lotes/incomplete-assigments/{id}',[TasksCropController::class,'GetIncompleteAssignments']);
    Route::get('/tasks-crops-lotes/daily-employees/{id}',[TasksCropController::class,'GetAssignedEmployees']);
    Route::post('/tasks-crops-lotes/close-assignment/{id}', [TasksCropController::class, 'CloseAssigment']);
    Route::post('/tasks-crops-lotes/close-daily-assigment/{id}', [TasksCropController::class, 'CloseDailyAssigment']);
    Route::post('/tasks-crops-lotes/register-daily-assigment',[TasksCropController::class,'RegisterDailyAssigment']);
    
    Route::apiResource('/employees',EmployeeController::class);

    Route::apiResource('/insumos',InsumosController::class);
    Route::get('/insumos-all',[InsumosController::class,'getAllInsumos']);
    Route::post('/insumos/upload', [InsumosController::class,'UploadInsumos']);
    Route::get('/dron-hours',[DashboardAgricolaController::class,'GetDronHours']);
    Route::get('/summary-hours-employees',[DashboardAgricolaController::class,'GetSummaryHoursEmployees']);
    Route::get('/tasks-in-progress',[DashboardAgricolaController::class,'GetTasksInProgress']);
    Route::get('/tasks-crops-in-progress',[DashboardAgricolaController::class,'GetTasksCropInProgress']);
    Route::get('/finished-tasks',[DashboardAgricolaController::class,'GetFinishedTasks']);
    Route::get('/finished-tasks-crop',[DashboardAgricolaController::class,'GetFinishedTasksCrop']);
    Route::get('/finished-total-tasks-finca',[DashboardAgricolaController::class,'GetFinishedTasksByFinca']);

    //CALIDAD
    Route::apiResource('/baskets',BasketController::class);
    Route::get('/baskets-all',[BasketController::class,'getAllBaskets']);
    Route::apiResource('/defects',DefectController::class);
    Route::get('/defects-by-product/{product_id}',[DefectController::class,'GetDefectsByProduct']);
    Route::apiResource('/products',ProductController::class);
    Route::get('/products-all',[ProductController::class,'GetAllProducts']);
    Route::apiResource('/variety-products',VarietyProductsController::class);
    Route::get('/variety-products-all',[VarietyProductsController::class,'GetAllVarieties']);
    Route::apiResource('/producers',ProducersController::class);
    Route::get('/producers-all',[ProducersController::class,'GetAllProducers']);
    Route::apiResource('/inspectors',InspectorController::class);
    Route::apiResource('/plantas',PlantasController::class);

    //ESTADOS DE CALIDAD
    Route::apiResource('/quality-statuses',QualityStatusesController::class);

    //TRANPORTISTAS
    Route::apiResource('/carriers',CarriersController::class);
    Route::get('/carriers-all',[CarriersController::class,'GetAllCarriers']);

    //PILOTOS
    Route::apiResource('/drivers',DriversController::class);
    Route::get('/drivers-by-carrier/{id}',[DriversController::class,'getAllPlatesByCarrier']);

    //PLACAS
    Route::apiResource('/plates',PlatesController::class);
    Route::get('/plates-by-carrier/{id}',[PlatesController::class,'getAllPlatesByCarrierId']);

    //CDPS de productores
    Route::apiResource('/productor-cdp',ProductorPlantationControlController::class);
    Route::get('/productor-cdp-all',[ProductorPlantationControlController::class,'GetAllProductorsCDPS']);

    //BOLETAS RECEPCIÓN MATERIA PRIMA
    Route::apiResource('/boleta-rmp',RmReceptionsController::class);
    Route::get('/boleta-rmp-all',[RmReceptionsController::class,'GetAllBoletas']);
    Route::get('/boleta-rmp/{id}/reject',[RmReceptionsController::class,'RejectBoleta']);
    Route::get('/boleta-rmp-info-doc/{id}',[RmReceptionsController::class,'GetInfoDoc']);
    Route::post('/boleta-rmp/prod/{id}',[RmReceptionsController::class,'updateProd']);
    Route::post('/boleta-rmp/calidad/{id}',[RmReceptionsController::class,'updateCalidad']);
    Route::post('/boleta-rmp/generate-grn/{id}',[RmReceptionsController::class,'GenerateGRN']);
    Route::apiResource('/transport-inspection',TransportInspectionsController::class);
    Route::apiResource('/transport-conditions',TransportConditionController::class);
    Route::get('/transport-conditions-all',[TransportConditionController::class,'getAllConditions']);
    Route::get('/rm-receptions-pending/quality-test',[DashboardCalidad::class,'ReceptionPedingQuality']);
    Route::get('/rm-receptions/by-percentage-diference',[DashboardCalidad::class,'ReceptionByPercentageDiference']);

    //PRODUCCIÓN
    Route::get('/employees-comodines',[EmployeeController::class,'getAllComodines']);

    Route::apiResource('/sku',SKUController::class);
    Route::get('/sku-all',[SKUController::class,'GetAllSKU']);
    
    Route::apiResource('/lines',LinesController::class);
    Route::post('/lines/update-positions/{id}',[LinesController::class,'UpdatePositions']);
    
    Route::get('/lines/performances-per-day/{id}',[LinesController::class,'GetPerformanceByLine']);

    Route::get('/lines-all',[LinesController::class,'GetAllLines']);
    Route::get('/lines-by-sku/{id}',[LinesController::class,'GetAllLinesBySku']);

    Route::apiResource('/lines-skus',LineStockKeepingUnitsController::class);

    Route::apiResource('/timeouts',TimeOutController::class);
    Route::get('/timeouts-all',[TimeOutController::class,'GetAllTimeouts']);

    Route::apiResource('/notes',TaskProductionPlanNotesController::class);

    Route::apiResource('/weekly_production_plan',WeeklyProductionPlanController::class);
    Route::get('/weekly_production_plan/details/{weekly_plan_id}/{line_id}',[WeeklyProductionPlanController::class,'GetTasksByLineId']);
    Route::get('/weekly_production_plan/details/{weekly_plan_id}',[WeeklyProductionPlanController::class,'GetTasksForCalendar']);
    Route::get('/weekly_production_plan/details-by-date/{weekly_plan_id}',[WeeklyProductionPlanController::class,'GetTasksByDate']);
    Route::get('/weekly_production_plan/hours-by-date/{weekly_plan_id}',[WeeklyProductionPlanController::class,'GetHoursByDate']);

    Route::post('/weekly_production_plan/assign/{id}',[WeeklyProductionPlanController::class,'createAssigments']);
    
    Route::apiResource('/task_production_plan',TaskProductionController::class);
    Route::get('/tasks_production_plan/finished/details/{id}',[TaskProductionController::class,'FinishedTaskDetails']);
    Route::get('/tasks_production_plan/details/{id}',[TaskProductionController::class,'TaskDetails']);
    Route::post('/tasks_production_plan/new-task',[TaskProductionController::class,'CreateNewTaskProduction']);
    Route::post('/tasks_production_plan/create-assignee/{id}',[TaskProductionController::class,'CreateAssignee']);
    Route::post('/tasks_production_plan/{id}/add-timeout/open',[TaskProductionController::class,'AddTimeOutOpen']);
    Route::post('/tasks_production_plan/{id}/add-timeout/close',[TaskProductionController::class,'AddTimeOutClose']);
    Route::post('/tasks_production_plan/{id}/assign',[TaskProductionController::class,'Assign']);
    Route::post('/tasks_production_plan/change-assignment',[TaskProductionController::class,'ChangeAssignment']);
    Route::post('/tasks_production_plan/{id}/performance',[TaskProductionController::class,'TakePerformance']);
    Route::put('/tasks_production_plan/change-priority',[TaskProductionController::class,'ChangePriority']);
    Route::patch('/tasks_production_plan/{id}/start',[TaskProductionController::class,'StartTaskProduction']);
    Route::patch('/tasks_production_plan/change-operation-date/{id}',[TaskProductionController::class,'ChangeOperationDate']);
    Route::patch('/tasks_production_plan/{id}/end',[TaskProductionController::class,'EndTaskProduction']);

    Route::apiResource('/employee-permissions',EmployeePermissionsController::class);
   
    Route::post('/report-production/{weekly_production_plan}/{line_id}',[ReportController::class,'PlanillaProduccion']);
});

//Autenticación
Route::apiResource('/login', AuthController::class);