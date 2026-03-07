<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Session\SessionController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);


Route::post('/register', [UserController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', [UserController::class, 'me']);

    Route::get('/sessions', [SessionController::class, 'devices']);

    Route::delete('/sessions', [SessionController::class, 'revokeSession']);
});
