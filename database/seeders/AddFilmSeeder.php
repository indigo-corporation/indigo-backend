<?php

namespace Database\Seeders;

use App\Models\Film\Film;
use Illuminate\Database\Seeder;

class AddFilmSeeder extends Seeder
{
    public function run()
    {
        $film = new Film([
            'original_title' => 'Druk',
            'ru' => [
                'title' => 'Еще по одной',
                'overview' => 'Four high-school teachers consume alcohol on a daily basis to see how it affects their social and professional lives.',
            ],
            'imdb_id' => 'tt10288566',
            'year'=> 2020
        ]);

        $film->save();
    }
}
