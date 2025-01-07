<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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
});


//Autenticación
Route::apiResource('/login', AuthController::class);