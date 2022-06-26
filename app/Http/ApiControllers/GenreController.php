<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\GenreResource;
use App\Models\Genre\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{

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

    public function show(Genre $genre)
    {
        return response()->success(
            new GenreResource($genre)
        );
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
