<?php

namespace Database\Seeders;

use App\Models\Genre\Genre;
use Illuminate\Database\Seeder;

class GenresHideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Genre::query()->whereIn('name', ['tv movie', 'animation'])->update(['is_hidden' => true]);
    }
}
