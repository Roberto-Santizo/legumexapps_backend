<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('/users', UsersController::class);
    Route::apiResource('/user', UserController::class);

    Route::get('/permissions/user', [PermissionController::class, 'userPermissions']);
    Route::apiResource('/permissions', PermissionController::class);

    Route::get('/roles/user', [RoleController::class, 'userRoles']);
    Route::apiResource('/roles', RoleController::class);
});
