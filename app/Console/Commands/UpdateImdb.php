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
        $left = Film::whereNotNull('imdb_id')
            ->whereNull('imdb_votes')
            ->orderBy('id', 'desc')
            ->count();
        dump(
            $left . ' left'
        );

        $chunkSize = 100;
        $i = 0;
        Film::whereNotNull('imdb_id')
            ->whereNull('imdb_votes')
            ->orderBy('id', 'desc')
            ->chunk($chunkSize, function (Collection $films) use (&$i, $chunkSize, &$left) {
                foreach ($films as $film) {
                    UpdateImdbJob::dispatchSync($film);
                    sleep(1);
                }

                dump('processed ' . ++$i * $chunkSize);

                if ($i * $chunkSize % 1000 === 0) {
                    $left -= 1000;

                    dump(
                        $left . ' left'
                    );
                    sleep(60);
                }
            });
    }
}
