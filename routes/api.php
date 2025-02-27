<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\BoletaRMPController;
use App\Http\Controllers\CDPController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\DashboardAgricolaController;
use App\Http\Controllers\DefectController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FincaController;
use App\Http\Controllers\InspectorController;
use App\Http\Controllers\InsumosController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PlantasController;
use App\Http\Controllers\ProducersController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QualityVarietyController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RmReceptionsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\TaskCropController;
use App\Http\Controllers\TasksCropController;
use App\Http\Controllers\TasksLoteController;
use App\Http\Controllers\TransportConditionController;
use App\Http\Controllers\TransportInspectionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VarietyProductsController;
use App\Http\Controllers\WeeklyPlanController;
use App\Models\VarietyProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

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
    Route::post('/insumos/upload', [InsumosController::class,'UploadInsumos']);

    Route::get('/dron-hours',[DashboardAgricolaController::class,'GetDronHours']);
    Route::get('/summary-hours-employees',[DashboardAgricolaController::class,'GetSummaryHoursEmployees']);
    Route::get('/tasks-in-progress',[DashboardAgricolaController::class,'GetTasksInProgress']);
    Route::get('/tasks-crops-in-progress',[DashboardAgricolaController::class,'GetTasksCropInProgress']);
    Route::get('/finished-tasks',[DashboardAgricolaController::class,'GetFinishedTasks']);
    Route::get('/finished-tasks-crop',[DashboardAgricolaController::class,'GetFinishedTasksCrop']);
    Route::get('/finished-total-tasks-finca',[DashboardAgricolaController::class,'GetFinishedTasksByFinca']);

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

    //PLANTAS
    Route::apiResource('/plantas',PlantasController::class);

    //BOLETAS RECEPCIÓN MATERIA PRIMA
    Route::apiResource('/boleta-rmp',RmReceptionsController::class);
    Route::get('/boleta-rmp-all',[RmReceptionsController::class,'GetAllBoletas']);
    Route::get('/boleta-rmp-info-doc/{id}',[RmReceptionsController::class,'GetInfoDoc']);
    Route::post('/boleta-rmp/prod/{id}',[RmReceptionsController::class,'updateProd']);
    Route::post('/boleta-rmp/calidad/{id}',[RmReceptionsController::class,'updateCalidad']);
    Route::post('/boleta-rmp/generate-grn/{id}',[RmReceptionsController::class,'GenerateGRN']);

    //BOLETAS DE CAMIONES   
    Route::apiResource('/transport-inspection',TransportInspectionsController::class);

    //CONDICIONES TRANPOSRTES
    Route::apiResource('/transport-conditions',TransportConditionController::class);
    Route::get('/transport-conditions-all',[TransportConditionController::class,'getAllConditions']);
});

//Autenticación
Route::apiResource('/login', AuthController::class);