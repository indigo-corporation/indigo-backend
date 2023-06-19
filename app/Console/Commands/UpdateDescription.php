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
        $left = Film::where('category', '<>', 'anime')
            ->whereNotNull('imdb_id')
            ->orderBy('id', 'desc')
            ->count();
        dump(
            $left . ' left'
        );

        $chunkSize = 100;
        $i = 0;
        Film::where('category', '<>', 'anime')
            ->whereNotNull('imdb_id')
            ->orderBy('id', 'desc')
            ->chunk($chunkSize, function (Collection $films) use (&$i, $chunkSize, &$left) {
                foreach ($films as $film) {
                    UpdateDescriptionJob::dispatchSync($film);
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
