<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/report/plans', [ReportController::class, 'DownloadReport']);
    Route::get('/report/insumos/{id}', [ReportController::class, 'DownloadReportInsumos']);
});
