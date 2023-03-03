<?php

namespace Database\Seeders;

use App\Models\Genre\Genre;
use Illuminate\Database\Seeder;

class AnimeGenresSeeder extends Seeder
{
    public function run()
    {
        $link = 'https://shikimori.one/api/genres';

        $data = json_decode(file_get_contents($link), true);

        foreach ($data as $item) {
            if ($item['kind'] !== 'anime') {
                continue;
            }

            Genre::create([
                'name' => strtolower($item['name']),
                'is_anime' => true,
                'ru' => [
                    'title' => strtolower($item['russian'])
                ]
            ]);
        }
    }
}
