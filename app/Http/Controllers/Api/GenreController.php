<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GenresGetRequest;
use App\Http\Resources\Api\GenreResource;
use App\Models\Genre\Genre;

class GenreController extends Controller
{
    public function index(GenresGetRequest $request)
    {
        $is_anime = (bool)$request->get('is_anime', false);
        $genres = Genre::getList($is_anime);

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
