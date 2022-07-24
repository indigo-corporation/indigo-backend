<?php

namespace Database\Seeders;

use App\Models\Genre\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenresSeeder extends Seeder
{
    public function run()
    {
        DB::table('genres')->truncate();

        $link1 = env('TMDB_API') . 'genre/movie/list?api_key=' . env('TMDB_KEY');
        $link2 = env('TMDB_API') . 'genre/movie/list?api_key=' . env('TMDB_KEY') . '&language=ru';

        $data1 = json_decode(file_get_contents($link1), true)['genres'];
        $data2 = json_decode(file_get_contents($link2), true)['genres'];

        foreach ($data1 as $key => $item) {
            Genre::create([
                'name' => strtolower($item['name']),
                'ru' => [
                    'title' => $data2[$key]['name']
                ]
            ]);
        }
    }
}
