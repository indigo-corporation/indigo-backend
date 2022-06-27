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
    Route::prefix('auth')->group(function () {
        Route::get('me', [\App\Http\ApiControllers\AuthController::class, 'me']);
        Route::post('logout', [\App\Http\ApiControllers\AuthController::class, 'logout']);
        Route::post('refresh', [\App\Http\ApiControllers\AuthController::class, 'refresh']);
    });


});

Route::prefix('films')->group(function () {
    Route::get('/search', [\App\Http\ApiControllers\FilmController::class, 'search']);
});
Route::resource('films', \App\Http\ApiControllers\FilmController::class);

Route::resource('genres', \App\Http\ApiControllers\GenreController::class);
