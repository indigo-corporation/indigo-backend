<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\GenreResource;
use App\Models\Genre\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{

    /**
     * @OA\Get (
     *     path="/genres",
     *     operationId="genresGet",
     *     tags={"Genres"},
     *     summary="Get genres",
     *     @OA\Response(
     *         response="200",
     *         description="Genres list",
     *         @OA\JsonContent(ref="#/components/schemas/GenresResource")
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
        return response()->success(
            GenreResource::collection(Genre::all())
        );
    }

    public function store(Request $request)
    {
        //
    }

    /**
     * @OA\Get (
     *     path="/genres/{id}",
     *     operationId="genreGet",
     *     tags={"Genres"},
     *     summary="Get genre",
     *     @OA\Response(
     *         response="200",
     *         description="Genre",
     *         @OA\JsonContent(ref="#/components/schemas/GenreResource")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/DefaultErrorResource")
     *     )
     * )
     *
     **/
    public function show(Genre $genre)
    {
        return response()->success(new GenreResource($genre));
    }

    public function update(Request $request, Genre $genre)
    {
        //
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();

        return response()->success(null, 204);
    }
}
