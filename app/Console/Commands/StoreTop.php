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

    private int $page;

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
            $this->page = $p;
            dump('=== page ===');
            dump($this->page);
            dump('============');

            try {
                $this->processPage();
            } catch (\Throwable $e) {
                dd($e->getMessage());
            }

            dump('ok');
            sleep(10);
        }
    }

    private function processPage()
    {
        $items = $this->getService->getTmdbFilmItems($this->page, true);

        $idsExists = $this->getImdbExists($items);

        $idField = 'imdb_id';

        foreach ($items as $item) {
            $item = $this->getService->getTmdbFilmItem($item->id, true);
            if ($item->$idField) {
                if (in_array($item->$idField, $idsExists)) {
                    continue;
                }

                dispatch(new FilmStoreJob($item->$idField));

                sleep(5);
            }
        }
    }

    private function getImdbExists($items)
    {
        $imdbIds = collect($items)->pluck('imdb_id')->toArray();

        return Film::whereIn('imdb_id', $imdbIds)->pluck('imdb_id')->toArray();
    }
}
