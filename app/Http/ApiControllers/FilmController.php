<?php

namespace App\Http\ApiControllers;

use App\Http\Requests\FilmIndexRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\FilmResource;
use App\Http\Resources\FilmShortResource;
use App\Http\Resources\PaginatedCollection;
use App\Models\Film\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FilmController extends Controller
{
    public const FILMS_PER_PAGE = 48;

    public const FILMS_LIMIT_MAIN = 18;

    public function main()
    {
        $response = Cache::remember('main', now()->addMinutes(10), function () {
            foreach (Film::CATEGORIES as $category) {
                $query = Film::with(['translations'])
                    ->where('is_hidden', false)
                    ->where('category', $category)
                    ->where('year', date("Y"));

                if ($category === Film::CATEGORY_ANIME) {
                    $query = $query->whereNotNull('shiki_rating')
                        ->orderBy('shiki_rating', 'DESC');
                } else {
                    $query = $query->whereNotNull('imdb_rating')
                        ->where('imdb_votes', '>=', Film::IMDB_VOTES_MIN)
                        ->whereHas('countries', function ($q) {
                            $q->whereNotIn('iso2', ['IN', 'RU', 'CN', 'KR', 'JP', 'TR']);
                        })
                        ->orderBy('imdb_rating', 'DESC');
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
                $response[$category] = FilmShortResource::collection($$category->shuffle());
            }

            return $response;
        });

        return response()->success($response);
    }

    public function index(FilmIndexRequest $request)
    {
        $category = $request->get('category');
        $sortField = $request->get('sort_field', Film::SORT_FIELD);
        $sortDirection = $request->get('sort_direction', Film::SORT_DIRECTION);

        $query = Film::with(['translations', 'countries'])
            ->where('is_hidden', false);

        if ($category) {
            $query = $query->where('category', $category);

            if ($category !== Film::CATEGORY_ANIME) {
                $query = $query->where('imdb_votes', '>=', Film::IMDB_VOTES_MIN);
            }
        }

        $query = $query->sort($sortField, $sortDirection);

        return response()->success_paginated(
            new PaginatedCollection($query->paginate(self::FILMS_PER_PAGE), FilmResource::class)
        );
    }

    public function show($filmId)
    {
        $film = Film::where('is_hidden', false)->find((int)$filmId);
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
        $films = Film::with(['translations'])
            ->where('is_hidden', false)
            ->whereTranslationIlike('title', '%' . $request->find . '%');

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

    public function getByGenre($genre_id, FilmIndexRequest $request)
    {
        $category = $request->get('category');
        $sortField = $request->get('sort_field', Film::SORT_FIELD);
        $sortDirection = $request->get('sort_direction', Film::SORT_DIRECTION);

        $query = Film::with(['translations'])
            ->where('is_hidden', false)
            ->whereHas('genres', function ($query) use ($genre_id) {
                $query->where('genres.id', $genre_id);
            });

        if ($category) {
            $query = $query->where('category', $category);

            if ($category !== Film::CATEGORY_ANIME) {
                $query = $query->where('imdb_votes', '>=', Film::IMDB_VOTES_MIN);
            }
        }

        $query = $query->sort($sortField, $sortDirection);

        return response()->success_paginated(
            new PaginatedCollection($query->paginate(self::FILMS_PER_PAGE), FilmShortResource::class)
        );
    }
}
