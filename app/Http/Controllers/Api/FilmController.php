<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\FilmIndexRequest;
use App\Http\Requests\Api\SearchRequest;
use App\Http\Resources\Api\CommentResource;
use App\Http\Resources\Api\FilmResource;
use App\Http\Resources\Api\FilmShortResource;
use App\Http\Resources\Api\PaginatedCollection;
use App\Managers\FilmSearchManager;
use App\Models\Film\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class FilmController extends Controller
{
    public const FILMS_PER_PAGE = 48;

    public const FILMS_LIMIT_MAIN = 18;

    public function main()
    {
        $response = Cache::remember('main', now()->addMinutes(15), function () {
            foreach (Film::CATEGORIES as $category) {
                $query = Film::with(['translations'])
                    ->where('is_hidden', false)
                    ->where('category', $category)
                    ->where('year', date('Y'));

                if ($category === Film::CATEGORY_ANIME) {
                    $query = $query->whereNotNull('shiki_rating')
                        ->orderBy('shiki_rating', 'DESC');
                } else {
                    $query = $query->whereNotNull('imdb_rating')
                        ->where('imdb_votes', '>=', Film::IMDB_VOTES_MIN)
                        ->whereHas('countries', function ($q) {
                            $q->whereNotIn('iso2', Film::HIDDEN_COUNTRIES);
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

        $perPage = self::FILMS_PER_PAGE;
        $page = $request->get('page', 1);

        $key = 'films-list:' . implode('_', [
            $category,
            $genreId,
            $year,
            $countryId,
            $sortField,
            $sortDirection,
            $perPage,
            $page
        ]);

        $films = Cache::remember($key, now()->addMinutes(15), function () use (
            $category,
            $genreId,
            $year,
            $countryId,
            $sortField,
            $sortDirection,
            $perPage,
            $page
        ) {
            $query = Film::getListQuery(
                $category,
                $genreId,
                $year,
                $countryId
            );

            if ($category) {
                if ($category !== Film::CATEGORY_ANIME) {
                    $query = $query->where('films.imdb_votes', '>=', Film::IMDB_VOTES_MIN);

                    $query = $query->where(function ($q) {
                        $q->doesntHave('countries')
                            ->orWhereHas('countries', function ($q) {
                                $q->whereNotIn('iso2', Film::HIDDEN_COUNTRIES);
                            });
                    });
                }
            }

            return $query
                ->sort($sortField, $sortDirection)
                ->paginate($perPage, ['*'], 'page', $page);
        });

        return response()->success_paginated(
            new PaginatedCollection($films, FilmResource::class)
        );
    }

    public function show(string $filmId)
    {
        $film = Cache::remember('film:' . $filmId, now()->addHour(), function () use ($filmId) {
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
        $searchManager = new FilmSearchManager($request->get('find'));

        if ($request->has('category')) {
            $searchManager->setCategory($request->get('category'));
        }

        if ($request->has('genre_id')) {
            $searchManager->setGenreId($request->get('genre_id'));
        }

        if ($request->has('year')) {
            $searchManager->setYear($request->get('year'));
        }

        if ($request->has('country_id')) {
            $searchManager->setCountryId($request->get('country_id'));
        }

        $films = $searchManager->getQuery()->paginate(self::FILMS_PER_PAGE);

        return response()->success_paginated(
            new PaginatedCollection($films, FilmShortResource::class)
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

    public function recommendations(string $filmId)
    {
        $recommendations = Cache::remember('film_recommendations:' . $filmId, now()->addHour(), function () use ($filmId) {
            $recommendations = [];
            $film = Film::with(['genres'])->find((int)$filmId);

            if ($film) {
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
                        if (in_array($country->iso2, Film::HIDDEN_COUNTRIES)) {
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
                            $q->whereNotIn('iso2', Film::HIDDEN_COUNTRIES);
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

    public function getDataForPlayer(string $filmId)
    {
        $data = Cache::remember('film_data_player:' . $filmId, now()->addHour(), function () use ($filmId) {

            $film = Film::find((int)$filmId);
            if (!$film) {
                abort(404);
            }

            $data = [];

            $filmFolder = 'videos/' . $film->id;
            $seasonFolders = Storage::disk('public')->directories($filmFolder);

            if (!$seasonFolders) {
                $urls = Storage::disk('public')->files($filmFolder);

                $files = [];
                foreach ($urls as $url) {
                    $q = last(explode('/', $url));
                    $q = explode('.', $q)[0];

                    $files[] = '[' . $q . 'p]' . url('storage/' . $url);
                }

                return [
                    'file' => implode(',', $files)
                ];
            }

            foreach ($seasonFolders as $key => $seasonFolder) {
                $season = last(explode('/', $seasonFolder));

                $data[$key] = [
                    'title' => 'Сезон ' . $season,
                    'folder' => []
                ];

                $episodeFolders = Storage::disk('public')->directories($seasonFolder);
                foreach ($episodeFolders as $i => $episodeFolder) {
                    $episode = last(explode('/', $episodeFolder));

                    $urls = Storage::disk('public')->files($episodeFolder);

                    $files = [];
                    foreach ($urls as $url) {
                        $q = last(explode('/', $url));
                        $q = explode('.', $q)[0];

                        $files[] = '[' . $q . 'p]' . url('storage/' . $url);
                    }

                    $data[$key]['folder'][$i] = [
                        'title' => 'Серия ' . $episode,
                        'file' => implode(',', $files)
                    ];
                }
            }

            return $data;
        });

        return response()->success($data);
    }
}
