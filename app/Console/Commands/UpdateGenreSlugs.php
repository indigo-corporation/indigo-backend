<?php

namespace App\Console\Commands;

use App\Models\Genre\Genre;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Console\Command;

class UpdateGenreSlugs extends Command
{
    protected $signature = 'update-genre-slugs';

    protected $description = 'update-genre-slugs';


    public function handle()
    {
       foreach (Genre::all() as $genre) {
           (new SlugService())->slug($genre, true);
           $genre->save();
       }
    }
}
