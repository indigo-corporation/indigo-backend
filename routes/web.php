<?php

use App\Jobs\AnimeStoreJob;
use App\Jobs\FilmStoreJob;
use App\Jobs\SerialStoreJob;
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

Route::get('/add-film', function () {
    $imdb_id = request()->imdb_id;

    if ($imdb_id) {
        dispatch(new FilmStoreJob($imdb_id));

        return 'ok';
    }

    return 'no imdb_id provided';
});

Route::get('/add-serial', function () {
    $imdb_id = request()->imdb_id;

    if ($imdb_id) {
        dispatch(new SerialStoreJob($imdb_id));

        return 'ok';
    }

    return 'no imdb_id provided';
});

Route::get('/add-anime', function () {
    $shiki_id = request()->shiki_id;

    if ($shiki_id) {
        dispatch(new AnimeStoreJob($shiki_id));

        return 'ok';
    }

    return 'no shiki_id provided';
});
