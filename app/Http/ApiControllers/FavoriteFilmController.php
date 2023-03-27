<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\FavoriteFilmsRequest;
use App\Http\Resources\FilmShortResource;
use App\Http\Resources\PaginatedCollection;
use App\Models\FavoriteFilm;
use App\Models\Film\Film;
use Illuminate\Support\Facades\Auth;

class FavoriteFilmController extends Controller
{
    public function all()
    {
        return response()->success_paginated(
            new PaginatedCollection(Auth::user()->favorite_films_films()->paginate(FilmController::FILMS_PER_PAGE), FilmShortResource::class)
        );
    }

    public function add(FavoriteFilmsRequest $request)
    {
        $user = Auth::user();

        $exists = FavoriteFilm::query()
            ->where('user_id', $user->id)
            ->where('film_id', $request->film_id)
            ->exists();

        if (!$exists) {
            $user->favorite_films()->create([
                'user_id' => $user->id,
                'film_id' => $request->film_id,
            ]);
        }

        return response()->success([
            'favorite_ids' => $user->favorite_film_ids
        ]);
    }

    public function remove(FavoriteFilmsRequest $request)
    {
        $user = Auth::user();

        $exists = FavoriteFilm::query()
            ->where('user_id', $user->id)
            ->where('film_id', $request->film_id)
            ->exists();

        if ($exists) {
            $user->favorite_films()->where([
                'user_id' => $user->id,
                'film_id' => $request->film_id,
            ])->delete();
        }

        return response()->success([
            'favorite_ids' => $user->favorite_film_ids
        ]);
    }
}
