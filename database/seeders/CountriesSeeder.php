<?php

namespace Database\Seeders;

use App\Models\Country\Country;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    public function run()
    {
        $link = env('TMDB_API') . 'configuration/countries?api_key=' . env('TMDB_KEY') . '&language=ru';

        $data = json_decode(file_get_contents($link), true);

        foreach ($data as $key => $item) {
            Country::create([
                'code' => $item['iso_3166_1'],
                'name' => $item['english_name'],
                'ru' => [
                    'title' => $item['native_name'],
                    'country_id' => $item['iso_3166_1']
                ]
            ]);
        }
    }
}
