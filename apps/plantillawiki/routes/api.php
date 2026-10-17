<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RolesController;
use App\Http\Controllers\Api\Carrera\CarreraController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout']);
        Route::post('/refresh', [LoginController::class, 'refresh']);
        Route::post('/me', [LoginController::class, 'me']);
        Route::prefix('roles')->group(function () {
            Route::get('/', [RolesController::class, 'index']);
            Route::post('/', [RolesController::class, 'store']);
            Route::put('/{id}', [RolesController::class, 'update']);
            Route::delete('/{id}', [RolesController::class, 'destroy']);
        });
        Route::prefix('permissions')->group(function () {
            Route::get('/', [RolesController::class, 'getPermissions']);
        });
    });
});

Route::middleware('auth:api')->prefix('carreras')->group(function () {
    Route::get('/', [CarreraController::class, 'index']);
    Route::post('/crear', [CarreraController::class, 'store']);
    Route::get('/{id}', [CarreraController::class, 'showById']);
    Route::put('/{id}', [CarreraController::class, 'update']);
    Route::delete('/{id}', [CarreraController::class, 'destroy']);
});
