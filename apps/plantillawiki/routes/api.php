<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RolesController;
use App\Http\Controllers\Api\Carrera\CarreraController;
use App\Http\Controllers\Api\Usuario\UserController;
use App\Http\Controllers\Api\Wiki\WikiCategoryController;
use App\Http\Controllers\Api\Wiki\WikiTagController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/refresh', [LoginController::class, 'refresh'])
    ->middleware('throttle:10,1');

Route::prefix('auth')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout']);
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
        Route::prefix('users')->group(function(){
            Route::get('/',[UserController::class,'index']);
        });
    });
});

Route::prefix('carreras')->group(function () {
    Route::get('/', [CarreraController::class, 'index']);
    Route::post('/crear', [CarreraController::class, 'store']);
    Route::get('/{id}', [CarreraController::class, 'showById']);
    Route::put('/{id}', [CarreraController::class, 'update']);
    Route::delete('/{id}', [CarreraController::class, 'destroy']);
});

Route::prefix('categorias')->group(function () {
    Route::get('/', [WikiCategoryController::class, 'index']);
    Route::post('/crear', [WikiCategoryController::class, 'store']);
    Route::get('/{id}', [WikiCategoryController::class, 'showById']);
    Route::put('/{id}', [WikiCategoryController::class, 'update']);
    Route::delete('/{id}', [WikiCategoryController::class, 'destroy']);
});

Route::prefix('tags')->group(function () {
    Route::get('/', [WikiTagController::class, 'index']);
    Route::post('/crear', [WikiTagController::class, 'store']);
    Route::get('/{id}', [WikiTagController::class, 'showById']);
    Route::put('/{id}', [WikiTagController::class, 'update']);
    Route::delete('/{id}', [WikiTagController::class, 'destroy']);
});


