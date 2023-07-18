<?php

namespace App\Console\Commands;

use App\Jobs\UpdateDescriptionJob;
use App\Models\Film\Film;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class UpdateDescription extends Command
{
    protected $signature = 'update-description {film_id?}';

    protected $description = 'update-description';


    public function handle()
    {
        $filmId = (int)$this->argument('film_id');
        if ($filmId) {
            $film = Film::find($filmId);
            UpdateDescriptionJob::dispatchSync($film);

            return;
        }

        $left = Film::where('category', '<>', 'anime')
            ->whereNotNull('imdb_id')
            ->orderBy('id', 'desc')
            ->count();
        dump(
            $left . ' left'
        );

        $chunkSize = 10;
        $i = 0;
        Film::where('category', '<>', 'anime')
            ->whereNotNull('imdb_id')
            ->orderBy('id', 'desc')
            ->chunk($chunkSize, function (Collection $films) use (&$i, $chunkSize, &$left) {
                foreach ($films as $film) {
                    UpdateDescriptionJob::dispatch($film);
                }

                dump('processed ' . ++$i * $chunkSize);
                sleep(1);

                if ($i * $chunkSize % 500 === 0) {
                    $left -= 500;

                    dump(
                        $left . ' left'
                    );
                    sleep(60);
                }
            });
    }
}
