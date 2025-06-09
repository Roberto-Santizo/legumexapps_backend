<?php

use App\Http\Controllers\BasketController;
use App\Http\Controllers\CarriersController;
use App\Http\Controllers\DashboardCalidad;
use App\Http\Controllers\DefectController;
use App\Http\Controllers\DriversController;
use App\Http\Controllers\PlantasController;
use App\Http\Controllers\PlatesController;
use App\Http\Controllers\ProducersController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductorPlantationControlController;
use App\Http\Controllers\QualityStatusesController;
use App\Http\Controllers\RmReceptionsController;
use App\Http\Controllers\TransportConditionController;
use App\Http\Controllers\TransportInspectionsController;
use App\Http\Controllers\VarietyProductsController;
use Illuminate\Support\Facades\Route;

Route::middleware('jwt.auth')->group(function () {
    Route::get('/baskets', [BasketController::class, 'index']);
    Route::get('/defects', [DefectController::class, 'index']);

    Route::apiResource('/products', ProductController::class);
    Route::apiResource('/variety-products', VarietyProductsController::class);
    Route::apiResource('/producers', ProducersController::class);
    Route::get('/plantas', [PlantasController::class, 'index']);

    Route::get('/quality-statuses', [QualityStatusesController::class, 'index']);

    Route::apiResource('/carriers', CarriersController::class);
    Route::apiResource('/drivers', DriversController::class);
    Route::apiResource('/plates', PlatesController::class);
    Route::apiResource('/productor-cdp', ProductorPlantationControlController::class);

    Route::apiResource('/boleta-rmp', RmReceptionsController::class);
    Route::get('/boleta-rmp-info-doc/{id}', [RmReceptionsController::class, 'GetInfoDoc']);
    Route::post('/boleta-rmp/prod/{id}', [RmReceptionsController::class, 'updateProd']);
    Route::post('/boleta-rmp/calidad/{id}', [RmReceptionsController::class, 'updateCalidad']);
    Route::post('/boleta-rmp/generate-grn/{id}', [RmReceptionsController::class, 'GenerateGRN']);
    Route::patch('/boleta-rmp/{id}/reject', [RmReceptionsController::class, 'RejectBoleta']);

    Route::apiResource('/transport-inspection', TransportInspectionsController::class);
    Route::apiResource('/transport-conditions', TransportConditionController::class);

    Route::get('/dashboard/calidad/rm-receptions-pending/quality-test', [DashboardCalidad::class, 'ReceptionPedingQuality']);
    Route::get('/dashboard/calidad/rm-receptions/by-percentage-diference', [DashboardCalidad::class, 'ReceptionByPercentageDiference']);
});
