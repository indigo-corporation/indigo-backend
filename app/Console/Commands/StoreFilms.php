<?php

namespace App\Console\Commands;

use App\Jobs\AnimeStoreJob;
use App\Jobs\FilmStoreJob;
use App\Jobs\SerialStoreJob;
use App\Models\Film\Film;
use App\Services\GetFromUrlService;
use Illuminate\Console\Command;

class StoreFilms extends Command
{
    protected $signature = 'store-films {category=film} {start_page=1} {end_page=50}';

    protected $description = 'store-films';

    private string $category;
    private int $page;

    private GetFromUrlService $getService;

    public function handle()
    {
        $this->category = $this->argument('category');
        $startPage = (int)$this->argument('start_page');
        $lastPage = (int)$this->argument('end_page');

        if (!in_array($this->category, Film::CATEGORIES)) {
            throw new \Error('wrong category');
        }

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
        $items = match ($this->category) {
            Film::CATEGORY_FILM => $this->getService->getCdnFilmItems($this->page, true),
            Film::CATEGORY_SERIAL => $this->getService->getCdnSerialItems($this->page, true),
            Film::CATEGORY_ANIME => $this->getService->getShikiItems($this->page, true),
        };

        $idsExists = match ($this->category) {
            Film::CATEGORY_FILM, Film::CATEGORY_SERIAL => $this->getImdbExists($items),
            Film::CATEGORY_ANIME => $this->getShikiExists($items),
        };

        $idField = match ($this->category) {
            Film::CATEGORY_FILM, Film::CATEGORY_SERIAL => 'imdb_id',
            Film::CATEGORY_ANIME => 'id',
        };

        foreach ($items as $item) {
            if ($item->$idField) {
                if (in_array($item->$idField, $idsExists)) {
                    continue;
                }

                match ($this->category) {
                    Film::CATEGORY_FILM => dispatch(new FilmStoreJob($item->$idField)),
                    Film::CATEGORY_SERIAL => dispatch(new SerialStoreJob($item->$idField)),
                    Film::CATEGORY_ANIME => dispatch(new AnimeStoreJob($item->$idField)),
                };

                sleep(5);
            }
        }
    }

    private function getImdbExists($items)
    {
        $imdbIds = collect($items)->pluck('imdb_id')->toArray();

        return Film::whereIn('imdb_id', $imdbIds)->pluck('imdb_id')->toArray();
    }

    private function getShikiExists($items)
    {
        $shikiIds = collect($items)->pluck('id')->toArray();

        return Film::whereIn('shiki_id', $shikiIds)->pluck('shiki_id')->toArray();
    }
}
