<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\FilmShortResource;
use App\Http\Resources\PaginatedCollection;
use App\Models\FavoriteFilms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteFilmsController extends Controller
{

    public function all()
    {
        return response()->success_paginated(
            new PaginatedCollection(Auth::user()->favorite_films_films()->paginate(20), FilmShortResource::class)
        );
    }

    public function add(Request $request)
    {
        $user = Auth::user();

        $exists = FavoriteFilms::query()
            ->where('user_id', $user->id)
            ->where('film_id', $request->film_id)
            ->exists();

        if (!$exists) {
            $user->favorite_films()->create([
                'user_id' => $user->id,
                'film_id' => $request->film_id,
            ]);
        }

        return response()->success(null, 201);
    }

    public function remove(Request $request)
    {
        $user = Auth::user();

        $exists = FavoriteFilms::query()
            ->where('user_id', $user->id)
            ->where('film_id', $request->film_id)
            ->exists();

        if ($exists) {
            $user->favorite_films()->where([
                'user_id' => $user->id,
                'film_id' => $request->film_id,
            ])->delete();
        }

        return response()->success(null, 204);
    }
}
