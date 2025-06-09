<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

//Autenticación
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/login', [AuthController::class, 'store']);
