<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannedUserController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FavoriteFilmController;
use App\Http\Controllers\Api\FilmController;
use App\Http\Controllers\Api\FilmStarController;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserContactController;
use App\Http\Controllers\Api\UserContactRequestController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorldController;
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
Route::group(['middleware' => ['guest']], function () {
    Route::prefix('auth')->group(function () {
        Route::post('/send-reset-password', [AuthController::class, 'sendResetPass']);
        Route::post('/reset-password', [AuthController::class, 'resetPass']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/telegram', [AuthController::class, 'telegramAuth']);
        Route::post('/google', [AuthController::class, 'googleAuth']);
    });
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    Route::prefix('users')->group(function () {
        Route::post('change-pass', [UserController::class, 'changePass']);
        Route::post('change-info', [UserController::class, 'changeInfo']);
        Route::post('change-picture', [UserController::class, 'changePicture']);
        Route::get('search', [UserController::class, 'search']);
    });

    Route::prefix('comments')->group(function () {
        Route::post('store', [CommentController::class, 'store']);
        Route::get('edit/{comment}', [CommentController::class, 'edit']);
        Route::post('update/{comment}', [CommentController::class, 'update']);
        Route::post('destroy/{comment}', [CommentController::class, 'destroy']);
        Route::post('like', [CommentController::class, 'like']);
        Route::post('unlike', [CommentController::class, 'unlike']);
    });

    Route::prefix('favorite-films')->group(function () {
        Route::get('all', [FavoriteFilmController::class, 'all']);
        Route::post('add', [FavoriteFilmController::class, 'add']);
        Route::post('remove', [FavoriteFilmController::class, 'remove']);
    });

    Route::prefix('film-stars')->group(function () {
        Route::get('get-by-film', [FilmStarController::class, 'getByFilm']);
        Route::post('add', [FilmStarController::class, 'add']);
        Route::post('remove', [FilmStarController::class, 'remove']);
    });

    Route::prefix('contacts')->group(function () {
        Route::get('all', [UserContactController::class, 'all']);
        Route::get('all-ids', [UserContactController::class, 'allIDs']);
        Route::post('remove', [UserContactController::class, 'remove']);
        Route::get('search', [UserContactController::class, 'search']);
    });

    Route::prefix('contact-requests')->group(function () {
        Route::get('outcomes', [UserContactRequestController::class, 'outcomes']);
        Route::get('incomes', [UserContactRequestController::class, 'incomes']);
        Route::get('ids', [UserContactRequestController::class, 'getIDs']);
        Route::post('create', [UserContactRequestController::class, 'create']);
        Route::post('{id}/destroy', [UserContactRequestController::class, 'destroy']);
        Route::post('{id}/accept', [UserContactRequestController::class, 'accept']);
    });

    Route::prefix('banned-users')->group(function () {
        Route::get('all', [BannedUserController::class, 'all']);
        Route::get('all-ids', [BannedUserController::class, 'allIDs']);
        Route::post('add', [BannedUserController::class, 'add']);
        Route::post('remove', [BannedUserController::class, 'remove']);
        Route::get('search', [BannedUserController::class, 'search']);
    });

    Route::prefix('chats')->group(function () {
        Route::get('get-by-user', [ChatController::class, 'getByUser']);
    });
    Route::resource('chats', ChatController::class);
    Route::resource('messages', MessageController::class);
});

Route::prefix('users')->group(function () {
    Route::get('{id}', [UserController::class, 'get']);
});

Route::prefix('films')->group(function () {
    Route::get('main', [FilmController::class, 'main']);
    Route::get('our-audio', [FilmController::class, 'ourAudio']);
    Route::get('loc', [FilmController::class, 'loc']);
    Route::get('search', [FilmController::class, 'search']);
    Route::get('{film}/get_comments', [FilmController::class, 'getComments']);
    Route::get('{film}/recommendations', [FilmController::class, 'recommendations']);
    Route::get('{film}/data-for-player', [FilmController::class, 'getDataForPlayer']);
});
Route::resource('films', FilmController::class);

Route::resource('genres', GenreController::class);

Route::prefix('world')->group(function () {
    Route::get('film-countries', [WorldController::class, 'filmCountries']);
    Route::get('countries-for-select', [WorldController::class, 'countriesForSelect']);
    Route::get('cities-for-select', [WorldController::class, 'citiesForSelect']);
});
