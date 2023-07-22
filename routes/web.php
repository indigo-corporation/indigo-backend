<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('webhooks')->group(function () {
    Route::post('telegram', [\App\Http\Controllers\Webhook\TelegramBotController::class, 'webhook']);
});

Route::prefix('admin')->group(function () {

    Route::prefix('films')->group(function () {
        Route::post('add-film', [\App\Http\Controllers\Admin\FilmController::class, 'addFilm']);
        Route::post('add-serial', [\App\Http\Controllers\Admin\FilmController::class, 'addSerial']);
        Route::post('add-anime', [\App\Http\Controllers\Admin\FilmController::class, 'addAnime']);
        Route::post('store-poster', [\App\Http\Controllers\Admin\FilmController::class, 'storePoster']);
    });
});
