<?php

use App\Http\ApiControllers\UserController;
use App\Http\ApiControllers\AuthController;
use App\Http\ApiControllers\CommentsController;
use App\Http\ApiControllers\FilmController;
use App\Http\ApiControllers\GenreController;
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

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    Route::prefix('users')->group(function () {
        Route::post('change-pass', [UserController::class, 'changePass']);
    });
});

Route::prefix('films')->group(function () {
    Route::get('/search', [FilmController::class, 'search']);
    Route::get('/{film}/get_comments', [FilmController::class, 'getComments']);
});
Route::resource('films', FilmController::class);

Route::resource('genres', GenreController::class);



Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::prefix('comments')->group(function (){
        Route::post('/store', [CommentsController::class, 'store']);
        Route::get('/edit/{comment}', [CommentsController::class, 'edit']);
        Route::post('/update/{comment}', [CommentsController::class, 'update']);
        Route::post('/destroy/{comment}', [CommentsController::class, 'destroy']);
    });
});

