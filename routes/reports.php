<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('jwt.auth')->group(function () {
    Route::post('/report/agricola/planification/{id}', [ReportController::class, 'DownloadPlanificationReport']);
    Route::post('/report/agricola/personal-details/{id}', [ReportController::class, 'DownloadPersonalDetailsReport']);
    Route::post('/report/agricola/insumos/{id}', [ReportController::class, 'DownloadReportInsumos']);
    Route::post('/report/agricola/planilla/{id}', [ReportController::class, 'DownloadReportPlanilla']);

    Route::get('/report-production/{weekly_production_plan}/{line_id}', [ReportController::class, 'PlanillaProduccion']);
    Route::get('/report-production/{weekly_production_draft_id}', [ReportController::class, 'DownloadWeeklyProductionDraftTasks']);
});
