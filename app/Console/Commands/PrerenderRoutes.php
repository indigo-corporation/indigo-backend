<?php

namespace App\Console\Commands;

use App\Jobs\PrenderInFileJob;
use App\Models\Film\Film;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class PrerenderRoutes extends Command
{
    protected $signature = 'prerender-routes';

    protected $description = 'prerender-routes';


    public function handle()
    {
        $chunkSize = 250;
        $i = 0;

        Film::with(['translations'])
            ->orderBy('id', 'desc')
            ->chunk($chunkSize, function (Collection $films) use (&$i, $chunkSize) {
                $data = [];
                foreach ($films as $film) {
                    $data[] = '/' . $film->category . '/' . $film->slug;
                }

                if ($data) {
                    dump('start processing ' . $chunkSize);
                    $timeStart = now();

                    PrenderInFileJob::dispatchSync($data);

                    $timeEnd = now();
                    dump('processed in ' . strtotime($timeEnd) - strtotime($timeStart));

                    dump('processed ' . ++$i * $chunkSize);
                    sleep(30);
                }
            });
    }
}
