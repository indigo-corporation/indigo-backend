<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Admin\FilmIdRequest;
use App\Http\Requests\Admin\ImdbIdRequest;
use App\Http\Requests\Admin\ShikiIdRequest;
use App\Jobs\AnimeStoreJob;
use App\Jobs\FilmStoreJob;
use App\Jobs\SerialStoreJob;
use App\Jobs\StorePosterJob;
use App\Models\Film\Film;

class FilmController extends Controller
{
    public function storePoster(FilmIdRequest $request)
    {
        $filmId = $request->get('film_id');
        $film = Film::findOrFail($filmId);

        StorePosterJob::dispatch($film);

        return response()->success();
    }

    public function addFilm(ImdbIdRequest $request)
    {
        $imdb_id = $request->get('imdb_id');

        if ($imdb_id) {
            if (Film::where('imdb_id', $imdb_id)->exists()) {
                return response()->error(['code' => 666, 'message' => 'exists'], 400);
            }

            dispatch(new FilmStoreJob($imdb_id, true));

            return response()->success();
        }

        return response()->error(['code' => 666, 'message' => 'no imdb_id provided'], 400);
    }

    public function addSerial(ImdbIdRequest $request)
    {
        $imdb_id = $request->get('imdb_id');

        if ($imdb_id) {
            if (Film::where('imdb_id', $imdb_id)->exists()) {
                return response()->error(['code' => 666, 'message' => 'exists'], 400);
            }

            dispatch(new SerialStoreJob($imdb_id, true));

            return response()->success();
        }

        return response()->error(['code' => 666, 'message' => 'no imdb_id provided'], 400);
    }

    public function addAnime(ShikiIdRequest $request)
    {
        $shiki_id = $request->get('shiki_id');

        if ($shiki_id) {
            if (Film::where('shiki_id', $shiki_id)->exists()) {
                return response()->error(['code' => 666, 'message' => 'exists'], 400);
            }

            dispatch(new AnimeStoreJob($shiki_id));

            return response()->success();
        }

        return response()->error(['code' => 666, 'message' => 'no shiki_id provided'], 400);
    }
}
