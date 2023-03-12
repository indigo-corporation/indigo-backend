<?php

namespace App\Console\Commands;

use App\Models\Film\Film;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class StorePosters extends Command
{
    protected $signature = 'store-posters';

    protected $description = 'store-posters';

    public function handle()
    {
        dump(Film::whereNull('poster_medium')->count() . ' left');

        $i = 0;
        Film::whereNull('poster_medium')->orderBy('id')->chunk(100, function (Collection $films) use (&$i) {
            foreach ($films as $film) {
                try {
                    $film->savePosterThumbs($film->poster);
                } catch (\Throwable $e) {
                    dump([
                        'id' => $film->id,
                        'error' => $e->getMessage(),
                    ]);

                    sleep(10);
                }
            }
            dump('processed ' . ++$i * 100);
            dump(Film::whereNull('poster_medium')->count() . ' left');
        });

        dump('completed');
    }
}
