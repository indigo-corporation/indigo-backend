<?php

namespace App\Console\Commands;

use App\Jobs\StorePosterJob;
use App\Models\Film\Film;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class StorePosters extends Command
{
    protected $signature = 'store-posters {film_id?}';

    protected $description = 'store-posters';

    public function handle()
    {
        $filmId = (int)$this->argument('film_id');
        if ($filmId) {
            $film = Film::find($filmId);
            StorePosterJob::dispatchSync($film);

            return;
        }

        dump(Film::whereNotNull('poster')->whereNull('poster_medium')->count() . ' left');

        $i = 0;
        Film::whereNotNull('poster')
            ->whereNull('poster_medium')
            ->orderBy('id')
            ->chunk(100, function (Collection $films) use (&$i) {
                foreach ($films as $film) {
                    try {
                        dump($film->id);
                        StorePosterJob::dispatch($film);
                    } catch (\Throwable $e) {
                        dump([
                            'id' => $film->id,
                            'error' => $e->getMessage(),
                        ]);

                        sleep(5);
                    }
                }
                dump('processed ' . ++$i * 100);
                dump(Film::whereNotNull('poster')->whereNull('poster_medium')->count() . ' left');
            });

        dump('completed');
    }
}
