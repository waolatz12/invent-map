<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {

        Route::post(
            '/register',
            [AuthController::class, 'register']
        );

        Route::post(
            '/login',
            [AuthController::class, 'login']
        );

        Route::middleware('auth:sanctum')->group(function () {

            Route::post(
                '/logout',
                [AuthController::class, 'logout']
            );

            Route::get(
                '/me',
                [AuthController::class, 'me']
            );
            Route::get(
                '/companies',
                [AuthController::class, 'companies']
            );

            Route::middleware([
                'auth:sanctum',
                'organization',
            ])->group(function () {

                Route::get(
                    '/products',
                    [ProductController::class, 'index']
                );
            });
        });
    });
});
