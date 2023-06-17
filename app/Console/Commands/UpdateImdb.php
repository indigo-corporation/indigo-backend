<?php

namespace App\Console\Commands;

use App\Jobs\UpdateImdbJob;
use App\Models\Film\Film;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class UpdateImdb extends Command
{
    protected $signature = 'update-imdb';

    protected $description = 'update-imdb';


    public function handle()
    {
        dump(
            Film::whereNotNull('imdb_id')
                ->whereNull('imdb_votes')
                ->orderBy('id', 'desc')
                ->count() . ' left'
        );

        $i = 0;
        Film::whereNotNull('imdb_id')
            ->whereNull('imdb_votes')
            ->orderBy('id', 'desc')
            ->chunk(4, function (Collection $films) use (&$i) {
                foreach ($films as $film) {
                    UpdateImdbJob::dispatch($film);
                }

                dump('processed ' . ++$i * 100);
                sleep(1);
            });
    }
}
