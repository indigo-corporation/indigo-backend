<?php

namespace App\Http\ApiControllers;

use App\Http\Resources\CountryShortResource;
use App\Models\Country;
use App\Models\Film\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorldController extends Controller
{
    public function filmCountries(Request $request)
    {
        $countries = Country::whereHas('films', function ($q) {
            $q->where('imdb_votes', '>=', Film::IMDB_VOTES_MIN);
        })->get();

        return response()->success(CountryShortResource::collection($countries));
    }

    public function countriesForSelect(Request $request)
    {
        $query = DB::table('countries')->select(['id', 'name']);

        if ($request->get('name')) {
            $query = $query->where('name', 'ilike', $request->get('name') . '%');
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
            $query = $query->where('name', 'ilike', $request->get('name') . '%');
        }

        $cities = $query->limit(5)->get();

        return response()->success(
            $cities
        );
    }
}
