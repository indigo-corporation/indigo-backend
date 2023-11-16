<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\Film\Film;
use App\Models\Genre\Genre;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SetFilmCategory extends Command
{
    protected $signature = 'set-film-category';

    protected $description = 'set-film-category';

    public function handle(): void
    {
        Film::whereNull('category')
            ->chunk(100, function (Collection $films) use (&$i) {
                foreach ($films as $film) {
                    $film->updateCategory();
                }
            });
    }
}
