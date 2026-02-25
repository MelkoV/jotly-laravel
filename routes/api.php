<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => [],
    'prefix' => 'v1',
], function () {

    /*
     * Client routes
     */
    Route::prefix('/user')->group(function () {
        Route::post('/sign-up', [\App\Http\API\v1\Controllers\UserController::class, 'signUp']);
        Route::post('/sign-in', [\App\Http\API\v1\Controllers\UserController::class, 'signIn']);
        Route::post('/refresh-token', [\App\Http\API\v1\Controllers\UserController::class, 'refreshToken']);
    });
    Route::middleware(\App\Http\Middleware\HandleJwtToken::class)->group(function () {
        Route::prefix('/user')->group(function () {
            Route::get('/profile', [\App\Http\API\v1\Controllers\UserController::class, 'profile']);
        });
    });

})->name('api.');

