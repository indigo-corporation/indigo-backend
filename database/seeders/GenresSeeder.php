<?php

namespace Database\Seeders;

use App\Models\Genre\Genre;
use App\Models\Genre\GenreTranslation;
use Illuminate\Database\Seeder;

class GenresSeeder extends Seeder
{
    public function run()
    {
        $link1 = env('TMDB_API') . 'genre/movie/list?api_key=' . env('TMDB_KEY');
        $link2 = env('TMDB_API') . 'genre/movie/list?api_key=' . env('TMDB_KEY') . '&language=ru';

        $data1 = json_decode(file_get_contents($link1), true)['genres'];
        $data2 = json_decode(file_get_contents($link2), true)['genres'];

        foreach ($data1 as $key => $item) {
            Genre::create([
                'id' => $item['id'],
                'name' => strtolower($item['name'])
            ]);

            GenreTranslation::create([
                'locale' => 'ru',
                'title' => $data2[$key]['name'],
                'genre_id' => $item['id']
            ]);
        }
    }
}
