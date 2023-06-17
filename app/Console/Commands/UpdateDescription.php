<?php

namespace App\Console\Commands;

use App\Jobs\UpdateDescriptionJob;
use App\Models\Film\Film;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class UpdateDescription extends Command
{
    protected $signature = 'update-description';

    protected $description = 'update-description';


    public function handle()
    {
        dump(
            Film::where('category', '<>', 'anime')
                ->whereNotNull('imdb_id')
                ->orderBy('id', 'desc')
                ->count() . ' left'
        );

        $i = 0;
        Film::where('category', '<>', 'anime')
            ->whereNotNull('imdb_id')
            ->orderBy('id', 'desc')
            ->chunk(100, function (Collection $films) use (&$i) {
                foreach ($films as $film) {
                    UpdateDescriptionJob::dispatch($film);
                }

                dump('processed ' . ++$i * 100);
                sleep(60);
            });
    }
}
