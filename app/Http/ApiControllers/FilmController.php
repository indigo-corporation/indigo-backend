<?php

namespace App\Http\ApiControllers;

use App\Http\Resources\FilmResource;
use App\Http\Resources\PaginatedCollection;
use App\Models\Film\Film;
use Illuminate\Support\Facades\Request;

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
    public function index()
    {
        return response()->success_paginated(
            new PaginatedCollection(Film::paginate(20), FilmResource::class)
        );
    }

    public function show(Film $film)
    {
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
}
