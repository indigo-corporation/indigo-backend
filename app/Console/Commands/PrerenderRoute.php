<?php

namespace App\Console\Commands;

use App\Jobs\PrenderInFileJob;
use App\Jobs\PrenderJob;
use App\Models\Film\Film;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class PrerenderRoute extends Command
{
    protected $signature = 'prerender-route {filmId}';

    protected $description = 'prerender-route';


    public function handle()
    {
        $film = Film::with(['translations'])
            ->where('id', $this->argument('filmId'))
            ->first();

        if ($film) {
            PrenderJob::dispatchSync('/' . $film->category . '/' . $film->slug);
        }
    }
}
