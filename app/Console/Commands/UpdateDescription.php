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

        $chunkSize = 5;
        $i = 0;
        Film::where('category', '<>', 'anime')
            ->whereNotNull('imdb_id')
            ->orderBy('id', 'desc')
            ->chunk($chunkSize, function (Collection $films) use (&$i, $chunkSize) {
                foreach ($films as $film) {
                    UpdateDescriptionJob::dispatch($film);
                }

                dump('processed ' . ++$i * $chunkSize);
                sleep(2);

                if ($i * $chunkSize % 250 === 0) {
                    dump(250 . ' - sleep');
                    sleep(60 * 5);
                }
            });
    }
}
