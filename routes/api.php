<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CDPController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\FincaController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WeeklyPlanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    
    //USUARIO
    Route::apiResource('/users', UserController::class);
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus']);


    Route::apiResource('/roles',RoleController::class);
    Route::apiResource('/permissions',PermissionController::class);
    Route::apiResource('/tareas', TareaController::class);
    Route::apiResource('cdps',CDPController::class);
    Route::apiResource('/recipes',RecipeController::class);
    Route::apiResource('/crops',CropController::class);
    Route::apiResource('/lotes', LoteController::class);
    Route::apiResource('/fincas',FincaController::class);
    Route::apiResource('/plans',WeeklyPlanController::class);
});



//Autenticación
Route::apiResource('/login', AuthController::class);