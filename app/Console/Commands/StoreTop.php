<?php

namespace App\Console\Commands;

use App\Jobs\FilmStoreJob;
use App\Models\Film\Film;
use App\Services\GetFromUrlService;
use Illuminate\Console\Command;

class StoreTop extends Command
{
    protected $signature = 'store-top {start_page=1} {end_page=50}';

    protected $description = 'store-top';

    private GetFromUrlService $getService;

    public function handle()
    {
        $startPage = (int)$this->argument('start_page');
        $lastPage = (int)$this->argument('end_page');

        if (
            $startPage <= 0
            || $lastPage <= 0
            || $startPage > $lastPage
        ) {
            throw new \Error('wrong page');
        }

        $this->getService = new GetFromUrlService();

        for ($p = $startPage; $p <= $lastPage; $p++) {
            dump('=== page ===');
            dump($p);
            dump('============');

            try {
                $items = $this->getService->getTmdbFilmItems($p, true);

                foreach ($items as $item) {
                    $item = $this->getService->getTmdbFilmItem($item->id, true);
                    if ($item->imdb_id) {
                        if (Film::where('imdb_id', $item->imdb_id)->exists()) {
                            continue;
                        }

                        dispatch(new FilmStoreJob($item->imdb_id));

                        sleep(5);
                    }
                }
            } catch (\Throwable $e) {
                dd($e->getMessage());
            }

            dump('ok');
            sleep(10);
        }
    }
}
