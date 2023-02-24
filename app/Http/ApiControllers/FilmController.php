<?php

namespace App\Http\ApiControllers;

use App\Http\Requests\SearchRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\FilmResource;
use App\Http\Resources\FilmShortResource;
use App\Http\Resources\PaginatedCollection;
use App\Models\Film\Film;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    const FILMS_PER_PAGE = 48;

    public function index(Request $request)
    {
        $type = $request->get('type');

        $query = Film::orderBy('id', 'DESC');

        if($type) {
            $query = Film::typeQuery($query, $type);
        }

        return response()->success_paginated(
            new PaginatedCollection($query->paginate(self::FILMS_PER_PAGE), FilmResource::class)
        );
    }

    public function show($filmId)
    {
        $film = Film::find((int)$filmId);
        if (!$film) {
            abort(404);
        }

        return response()->success(new FilmResource($film));
    }

    public function store($request)
    {
        $film = Film::create($request->all());

        return response()->success($film, 201);
    }

    public function update(Request $request, Film $film)
    {
        $film->update($request->all());

        return response()->success($film);
    }

    public function destroy(Film $film)
    {
        $film->delete();

        return response()->success(null, 204);
    }

    public function search(SearchRequest $request)
    {
        $films = Film::whereTranslationIlike('title', '%' . $request->find . '%');

        return response()->success_paginated(
            new PaginatedCollection($films->paginate(self::FILMS_PER_PAGE), FilmShortResource::class)
        );
    }

    public function getComments($filmId)
    {
        $film = Film::find((int)$filmId);
        if (!$film) {
            abort(404);
        }

        return response()->success_paginated(
            new PaginatedCollection($film->comments()->paginate(20), CommentResource::class)
        );
    }

    public function getByGenre($genre_id, Request $request)
    {
        $query = Film::whereHas('genres', function ($query) use ($genre_id) {
            $query->where('genres.id', $genre_id);
        });

        $type = $request->get('type');
        if($type) {
            $query = Film::typeQuery($query, $type);
        }

        return response()->success_paginated(
            new PaginatedCollection($query->paginate(self::FILMS_PER_PAGE), FilmShortResource::class)
        );
    }


}
