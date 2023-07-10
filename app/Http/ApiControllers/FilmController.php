<?php

namespace App\Http\ApiControllers;

use App\Http\Requests\FilmIndexRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\FilmResource;
use App\Http\Resources\FilmShortResource;
use App\Http\Resources\PaginatedCollection;
use App\Models\Film\Film;
use App\Services\ElasticService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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
        $genreId = $request->get('genre_id');
        $year = $request->get('year');
        $countryId = $request->get('country_id');

        $sortField = $request->get('sort_field', Film::SORT_FIELD);
        $sortDirection = $request->get('sort_direction', Film::SORT_DIRECTION);

        $films = Film::getList(
            $category,
            $genreId,
            $year,
            $countryId,
            $sortField,
            $sortDirection,
            self::FILMS_PER_PAGE
        );

        return response()->success_paginated(
            new PaginatedCollection($films, FilmResource::class)
        );
    }

    public function show(string $filmId)
    {
        $film = Cache::remember('film:' . $filmId, now()->addHours(12), function () use ($filmId) {
            return Film::where('is_hidden', false)->find((int)$filmId);
        });

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

    public function search2(SearchRequest $request)
    {
        $page = $request->get('page', 1);

        $client = (new ElasticService())->getClient();

        $response = $client->search([
            'index' => 'films',
            'type' => 'anime',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $request->find,
                        'fields' => ['original_title', 'translations.title'],
                        'fuzziness' => 'auto:4,6'
                    ]
                ],
                'min_score' => 6
            ],
            'from' => ($page - 1) * self::FILMS_PER_PAGE,
            'size' => self::FILMS_PER_PAGE
        ]);

        $filmIds = collect($response['hits']['hits'])->pluck(['_id'])->toArray();

        $films = Film::with(['translations'])
            ->whereIn('id', $filmIds)
            ->orderByRaw("array_position('{" . implode(',', $filmIds) . "}'::int[], id)")
            ->get();

        $pagination = new LengthAwarePaginator(
            $films,
            $response['hits']['total']['value'],
            self::FILMS_PER_PAGE,
            $page
        );

        return response()->success_paginated(
            new PaginatedCollection($pagination, FilmShortResource::class)
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

    public function recommendations(string $filmId)
    {
        $recommendations = Cache::remember('film_recommendations:' . $filmId, now()->addHours(3), function () use ($filmId) {
            $recommendations = [];
            $film = Film::with(['genres'])->find((int)$filmId);

            if($film) {
                $query = Film::with(['translations'])
                    ->where('id', '<>', $film->id)
                    ->where('category', $film->category)
                    ->where('is_hidden', false);

                if ($film->genres->isNotEmpty()) {
                    $genre = $film->genres->first();

                    $query = $query
                        ->whereHas('genres', function ($query) use ($genre) {
                            $query->where('genres.id', $genre->id);
                        });
                }

                if ($film->year) {
                    $query = $query
                        ->where('year', '>=', $film->year - 5)
                        ->where('year', '<=', $film->year + 5);
                }

                if ($film->category === Film::CATEGORY_ANIME) {
                    $query = $query->whereNotNull('shiki_rating')
                        ->orderBy('shiki_rating', 'DESC');
                } else {
                    $countryCode = '';
                    foreach ($film->countries as $country) {
                        if (in_array($country->iso2, ['IN', 'RU', 'CN', 'KR', 'JP', 'TR'])) {
                            $countryCode = $country->iso2;

                            break;
                        }
                    }

                    if ($countryCode) {
                        $query = $query->whereHas('countries', function ($q) use ($countryCode) {
                            $q->where('iso2', $countryCode);
                        });
                    } else {
                        $query = $query->whereHas('countries', function ($q) {
                            $q->whereNotIn('iso2', ['IN', 'RU', 'CN', 'KR', 'JP', 'TR']);
                        });
                    }

                    $query = $query->whereNotNull('imdb_rating')
                        ->where('imdb_votes', '>=', 10000)
                        ->orderBy('imdb_rating', 'DESC');
                }

                $recommendations = $query
                    ->limit(20)
                    ->get();
            }

            return $recommendations
                ? collect($recommendations)->shuffle()->take(6)
                : collect();
        });

        return response()->success(FilmShortResource::collection($recommendations));
    }
}
