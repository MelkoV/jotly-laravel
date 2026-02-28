<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => [],
    'prefix' => 'v1',
], function () {

    Route::prefix('/user')->group(function () {
        Route::post('/sign-up', [\App\Http\API\v1\Controllers\UserController::class, 'signUp']);
        Route::post('/sign-in', [\App\Http\API\v1\Controllers\UserController::class, 'signIn']);
        Route::middleware(\App\Http\Middleware\HandleRefreshJwtToken::class)->group(function () {
            Route::post('/refresh-token', [\App\Http\API\v1\Controllers\UserController::class, 'refreshToken']);
        });
    });
    Route::middleware(\App\Http\Middleware\HandleJwtToken::class)->group(function () {
        Route::prefix('/user')->group(function () {
            Route::get('/profile', [\App\Http\API\v1\Controllers\UserController::class, 'profile']);
        });
        Route::prefix('/lists')->group(function () {
            Route::get('/', [\App\Http\API\v1\Controllers\ListController::class, 'index']);
            Route::post('/', [\App\Http\API\v1\Controllers\ListController::class, 'create']);
            Route::get('/delete-types/{id}', [\App\Http\API\v1\Controllers\ListController::class, 'deleteTypes']);
            Route::delete('/left/{id}', [\App\Http\API\v1\Controllers\ListController::class, 'left']);
            Route::get('/{id}', [\App\Http\API\v1\Controllers\ListController::class, 'view']);
            Route::put('/{id}', [\App\Http\API\v1\Controllers\ListController::class, 'update']);
            Route::delete('/{id}', [\App\Http\API\v1\Controllers\ListController::class, 'delete']);
        });
        Route::prefix('/list-items')->group(function () {
            Route::post('/', [\App\Http\API\v1\Controllers\ListItemController::class, 'create']);
            Route::put('/complete/{id}', [\App\Http\API\v1\Controllers\ListItemController::class, 'complete']);
            Route::put('/{id}', [\App\Http\API\v1\Controllers\ListItemController::class, 'update']);
            Route::delete('/{id}', [\App\Http\API\v1\Controllers\ListItemController::class, 'delete']);
        });
    });

})->name('api.');

