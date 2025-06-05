<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/report/plans', [ReportController::class, 'DownloadReport']);
    Route::get('/report/insumos/{id}', [ReportController::class, 'DownloadReportInsumos']);
    Route::get('/report/planilla/{id}', [ReportController::class, 'DownloadReportPlanilla']);
    
    Route::post('/report-production/{weekly_production_plan}/{line_id}', [ReportController::class, 'PlanillaProduccion']);
});
