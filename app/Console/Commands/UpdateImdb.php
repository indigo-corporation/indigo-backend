<?php

namespace App\Console\Commands;

use App\Models\Film\Film;
use App\Services\GetFromUrlService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class UpdateImdb extends Command
{
    protected $signature = 'update-imdb';

    protected $description = 'update-imdb';


    public function handle()
    {
        $getService = new GetFromUrlService();

        $i = 0;
        Film::whereNotNull('imdb_id')
            ->orderBy('id', 'desc')
            ->chunk(100, function (Collection $films) use (&$i, $getService) {
                foreach ($films as $film) {
                    $imdbData = $getService->getImdb($film->imdb_id, true);

                    if (!$imdbData) {
                        continue;
                    }

                    dump($film->imdb_id);

                    try {
                        $rating = $imdbData->rating() ?: null;
                        $votes = $imdbData->votes();
                    } catch (\Throwable $e) {
                        if (Str::contains($e->getMessage(), 'Status code [404]')) {
                            dump('not found');

                            continue;
                        }

                        throw $e;
                    }

                    $film->imdb_rating = $rating;
                    $film->imdb_votes = $votes;
                    $film->save();

                    sleep(3);
                }

                dump('processed ' . ++$i * 100);
                sleep(15);
            });
    }
}
