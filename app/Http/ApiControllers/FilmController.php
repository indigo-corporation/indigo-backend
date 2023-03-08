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
    public const FILMS_LIMIT_MAIN = 18;

    public function main()
    {
        foreach (Film::CATEGORIES as $category) {
            $query = Film::where('category', $category)
                ->where('year', 2023);

            if ($category === Film::CATEGORY_ANIME) {
                $query = $query->whereNotNull('shiki_id')
                    ->orderBy('shiki_id', 'DESC');
            } else {
                $query = $query->whereNotNull('imdb_id')
                    ->orderBy('imdb_id', 'DESC');
            }

            $$category = $query->limit(self::FILMS_LIMIT_MAIN)->get();
        }

        $new = [];
        $iCount = round(self::FILMS_LIMIT_MAIN / count(Film::CATEGORIES));
        for ($i = 0; $i <= $iCount; $i++) {
            foreach (Film::CATEGORIES as $category) {
                if (isset($$category[$i])) {
                    $new[] = $$category[$i];
                }
            }
        }
        $new = collect($new)->shuffle();

        $response = [
            'new' => FilmShortResource::collection($new)
        ];
        foreach (Film::CATEGORIES as $category) {
            $response[$category] = FilmShortResource::collection($$category);
        }

        return response()->success($response);
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
