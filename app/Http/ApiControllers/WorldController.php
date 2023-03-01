<?php

namespace App\Http\ApiControllers;

use App\Http\Resources\CommentResource;
use App\Http\Resources\FilmResource;
use App\Http\Resources\FilmShortResource;
use App\Http\Resources\PaginatedCollection;
use App\Models\Film\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nnjeim\World\World;

class WorldController extends Controller
{
    public function countriesForSelect(Request $request)
    {
        $query = DB::table('countries')->select(['id', 'name']);

        if ($request->get('name')) {
            $query = $query->where('name', 'ilike' ,$request->get('name') . '%');
        }

        $countries = $query->limit(5)->get();

        return response()->success(
            $countries
        );
    }

    public function citiesForSelect(Request $request)
    {
        $query = DB::table('cities')
            ->select(['id', 'name'])
            ->where('country_id', $request->get('country_id'));

        if ($request->get('name')) {
            $query = $query->where('name', 'ilike' ,$request->get('name') . '%');
        }

        $cities = $query->limit(5)->get();

        return response()->success(
            $cities
        );
    }
}
