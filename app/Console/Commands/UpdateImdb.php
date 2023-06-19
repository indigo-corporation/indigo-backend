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

        $chunkSize = 4;
        $i = 0;
        Film::whereNotNull('imdb_id')
            ->whereNull('imdb_votes')
            ->orderBy('id', 'desc')
            ->chunk($chunkSize, function (Collection $films) use (&$i, $chunkSize) {
                foreach ($films as $film) {
                    UpdateImdbJob::dispatch($film);
                }

                dump('processed ' . ++$i * $chunkSize);
                sleep(1);

                if ($i * $chunkSize % 250 === 0) {
                    dump(500 . ' - sleep');
                    sleep(60 * 5);
                }
            });
    }
}
