<?php

use App\Http\Controllers\InsumosReceptionController;
use App\Http\Controllers\PackingMaterialReceptionControlller;
use App\Http\Controllers\PackingMaterialsController;
use App\Http\Controllers\PackingMaterialTransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('jwt.auth')->group(function () {

    Route::apiResource('/packing-materials', PackingMaterialsController::class);
    Route::post('/packing-materials/upload', [PackingMaterialsController::class, 'UploadPackingMaterials']);

    Route::apiResource('/packing-material-transaction', PackingMaterialTransactionController::class);
    Route::apiResource('/packing-material-reception', PackingMaterialReceptionControlller::class);
    Route::apiResource('/insumos-reception', InsumosReceptionController::class);
});
