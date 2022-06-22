<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/auth/register', [\App\Http\ApiControllers\AuthController::class, 'register']);
Route::post('/auth/login', [\App\Http\ApiControllers\AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/auth/me', [\App\Http\ApiControllers\AuthController::class, 'me']);
    Route::post('/auth/logout', [\App\Http\ApiControllers\AuthController::class, 'logout']);
    Route::get('/auth/refresh', [\App\Http\ApiControllers\AuthController::class, 'refresh']);
});

Route::prefix('films')->group(function () {
    Route::get('/', [\App\Http\ApiControllers\FilmController::class, 'index']);
    Route::get('/{id}', [\App\Http\ApiControllers\FilmController::class, 'show']);
    Route::post('/', [\App\Http\ApiControllers\FilmController::class, 'store']);
    Route::put('/{id}', [\App\Http\ApiControllers\FilmController::class, 'update']);
    Route::delete('/{di}', [\App\Http\ApiControllers\FilmController::class, 'destroy']);
});
