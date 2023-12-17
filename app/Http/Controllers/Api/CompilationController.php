<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FavoriteFilmsRequest;
use App\Http\Resources\Api\CompilationResource;
use App\Http\Resources\Api\FilmShortResource;
use App\Http\Resources\Api\PaginatedCollection;
use App\Models\Compilation\Compilation;
use App\Models\FavoriteFilm;
use Illuminate\Support\Facades\Auth;

class CompilationController extends Controller
{
    public function index()
    {
        return response()->success_paginated(
            new PaginatedCollection(Compilation::orderBy('order')->paginate(), CompilationResource::class)
        );
    }

    public function show(Compilation $compilation)
    {
        return new CompilationResource($compilation);
    }
}
