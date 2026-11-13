<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RolesController;
use App\Http\Controllers\Api\Carrera\CarreraController;
use App\Http\Controllers\Api\Evento\EventController;
use App\Http\Controllers\Api\Usuario\UserController;
use App\Http\Controllers\Api\Evento\EventSubjectController;
use App\Http\Controllers\Api\Wiki\WikiCategoryController;
use App\Http\Controllers\Api\Wiki\WikiTagController;
use App\Http\Controllers\Api\Wiki\WikiArticleController;
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

Route::prefix('eventos')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::post('/crear', [EventController::class, 'store']);
    Route::get('/activo', [EventController::class, 'active']);
    Route::get('/{id}', [EventController::class, 'showById']);
    Route::put('/{id}', [EventController::class, 'update']);
    Route::delete('/{id}', [EventController::class, 'destroy']);
    Route::patch('/{id}/activar', [EventController::class, 'toggleActive']);

    Route::prefix('/{eventId}/materias')->group(function () {
        Route::get('/', [EventSubjectController::class, 'index']);
        Route::post('/crear', [EventSubjectController::class, 'store']);
        Route::get('/{id}', [EventSubjectController::class, 'showById']);
        Route::put('/{id}', [EventSubjectController::class, 'update']);
        Route::delete('/{id}', [EventSubjectController::class, 'destroy']);
        Route::get('/{id}/proyectos', [EventSubjectController::class, 'projects']);
    });
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

Route::prefix('articulos')->group(function () {
    Route::get('/', [WikiArticleController::class, 'index']);
    Route::post('/crear', [WikiArticleController::class, 'store']);
    Route::get('/{id}', [WikiArticleController::class, 'showById']);
    Route::put('/{id}', [WikiArticleController::class, 'update']);
    Route::delete('/{id}', [WikiArticleController::class, 'destroy']);

});
