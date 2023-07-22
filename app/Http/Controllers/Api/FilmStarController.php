<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FavoriteFilmsRequest;
use App\Http\Requests\Api\FilmStarStoreRequest;
use App\Models\Film\Film;
use App\Models\FilmStar;
use Illuminate\Support\Facades\Auth;

class FilmStarController extends Controller
{
    public function getByFilm(FavoriteFilmsRequest $request)
    {
        $user = Auth::user();

        $star = FilmStar::query()
            ->where('film_id', $request->film_id)
            ->first();

        return response()->success($star);
    }

    public function add(FilmStarStoreRequest $request)
    {
        $user = Auth::user();

        $star = FilmStar::firstOrCreate([
            'user_id' => $user->id,
            'film_id' => $request->film_id
        ]);

        $star->count = $request->count;
        $star->save();

        $filmStars = Film::find($request->film_id)->stars()->avg('count');

        return response()->success($filmStars);
    }

    public function remove(FavoriteFilmsRequest $request)
    {
        $user = Auth::user();

        FilmStar::query()
            ->where('user_id', $user->id)
            ->where('film_id', $request->film_id)
            ->delete();

        $filmStars = Film::find($request->film_id)->stars()->avg('count');

        return response()->success($filmStars);
    }
}
