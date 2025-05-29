<?php

use App\Http\Controllers\InsumosReceptionController;
use App\Http\Controllers\PackingMaterialReceptionControlller;
use App\Http\Controllers\PackingMaterialsController;
use App\Http\Controllers\PackingMaterialTransactionController;
use App\Http\Controllers\PackingMaterialWastagesController;
use App\Http\Controllers\SuppliersPackingMaterialController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('/packing-materials', PackingMaterialsController::class);
    Route::apiResource('/suppliers-packing-material', SuppliersPackingMaterialController::class);

    Route::apiResource('/packing-material-transaction', PackingMaterialTransactionController::class);
    Route::apiResource('/packing-material-reception', PackingMaterialReceptionControlller::class);
    Route::apiResource('/insumos-reception', InsumosReceptionController::class);
});
