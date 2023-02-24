<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenresGetRequest;
use App\Http\Resources\GenreResource;
use App\Models\Genre\Genre;

class GenreController extends Controller
{
    public function index(GenresGetRequest $request)
    {
        $is_anime = (bool)$request->get('is_anime', false);
        $genres = Genre::where('is_anime', $is_anime)
            ->where('is_hidden', false)
            ->get();

        return response()->success(
            GenreResource::collection($genres)
        );
    }

    public function show(Genre $genre)
    {
        return response()->success(new GenreResource($genre));
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();

        return response()->success(null, 204);
    }
}
