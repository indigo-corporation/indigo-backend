<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\CountryShortResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorldController extends Controller
{
    public function filmCountries(Request $request)
    {
        $countries = Country::getList();

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
