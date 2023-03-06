<?php

namespace App\Http\ApiControllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\FilmResource;
use App\Http\Resources\FilmShortResource;
use App\Http\Resources\PaginatedCollection;
use App\Models\Film\Film;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    public const FILMS_PER_PAGE = 48;
    public const FILMS_LIMIT_MAIN = 12;

    public function main()
    {
        $new = Film::orderBy('release_date', 'DESC')->limit(self::FILMS_LIMIT_MAIN)->get();
        $film = Film::orderBy('imdb_id', 'DESC')
            ->where('category', Film::CATEGORY_FILM)
            ->where('year', 2023)
            ->limit(self::FILMS_LIMIT_MAIN)
            ->get();
        $serial = Film::orderBy('imdb_id', 'DESC')
            ->where('category', Film::CATEGORY_SERIAL)
            ->where('year', 2023)
            ->limit(self::FILMS_LIMIT_MAIN)
            ->get();
        $anime = Film::orderBy('imdb_id', 'DESC')
            ->where('category', Film::CATEGORY_ANIME)
            ->where('year', 2023)
            ->limit(self::FILMS_LIMIT_MAIN)
            ->get();
        $cartoon = Film::orderBy('imdb_id', 'DESC')
            ->where('category', Film::CATEGORY_CARTOON)
            ->where('year', 2023)
            ->limit(self::FILMS_LIMIT_MAIN)
            ->get();

        return response()->success([
            'new' => FilmShortResource::collection($new),
            'films' => FilmShortResource::collection($film),
            'serials' => FilmShortResource::collection($serial),
            'anime' => FilmShortResource::collection($anime),
            'cartoons' => FilmShortResource::collection($cartoon),
        ]);
    }

    public function index(CategoryRequest $request)
    {
        $category = $request->get('category');

        $query = Film::orderBy('release_date', 'DESC');

        if ($category) {
            $query = $query->where('category', $category);
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

    public function getByGenre($genre_id, CategoryRequest $request)
    {
        $query = Film::whereHas('genres', function ($query) use ($genre_id) {
            $query->where('genres.id', $genre_id);
        });

        $category = $request->get('category');
        if ($category) {
            $query = $query->where('category', $category);
        }

        return response()->success_paginated(
            new PaginatedCollection($query->paginate(self::FILMS_PER_PAGE), FilmShortResource::class)
        );
    }
}
