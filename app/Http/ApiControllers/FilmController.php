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

    /**
     * @OA\Get (
     *     path="/films",
     *     operationId="filmsGet",
     *     tags={"Films"},
     *     summary="Get films",
     *     @OA\Response(
     *         response="200",
     *         description="Films list",
     *         @OA\JsonContent(ref="#/components/schemas/FilmsResource")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/DefaultErrorResource")
     *     )
     * )
     *
     **/
    public function index(Request $request)
    {
        $type = $request->get('type');

        $query = Film::orderBy('id', 'DESC');

        if($type) {
            $query = Film::typeQuery($query, $type);
        }

        return response()->success_paginated(
            new PaginatedCollection($query->paginate(20), FilmResource::class)
        );
    }

    /**
     * @OA\Get (
     *     path="/films/{id}",
     *     operationId="filmGet",
     *     tags={"Films"},
     *     summary="Get film",
     *     @OA\Response(
     *         response="200",
     *         description="Film",
     *         @OA\JsonContent(ref="#/components/schemas/FilmResource")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/DefaultErrorResource")
     *     )
     * )
     *
     **/
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

    /**
     * @OA\Get (
     *     path="/films/search",
     *     operationId="filmsSearch",
     *     tags={"Films"},
     *     summary="Search films",
     *     @OA\Response(
     *         response="200",
     *         description="Films list",
     *         @OA\JsonContent(ref="#/components/schemas/FilmsResource")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/DefaultErrorResource")
     *     )
     * )
     *
     **/
    public function search(SearchRequest $request)
    {
        $films = Film::whereTranslationIlike('title', '%' . $request->find . '%');

        return response()->success_paginated(
            new PaginatedCollection($films->paginate(20), FilmShortResource::class)
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
            new PaginatedCollection($query->paginate(20), FilmShortResource::class)
        );
    }


}
