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
    protected $signature = 'store-films {category=film} {start_page=1} {end_page?}';

    protected $description = 'store-films';

    private string $category;

    private int $page;

    private bool $onlyNew = false;

    private GetFromUrlService $getService;

    public function handle()
    {
        $this->category = $this->argument('category');
        $startPage = (int)$this->argument('start_page');
        $lastPage = (int)$this->argument('end_page');

        if (!$lastPage) {
            $this->onlyNew = true;
            $lastPage = 50;
        }

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
                if ($this->processPage() === false) {
                    dump('New films are out');

                    return;
                }
            } catch (\Throwable $e) {
                dd($e->getMessage());
            }

            dump('ok');
            sleep(10);
        }
    }

    private function processPage(): bool
    {
        $items = match ($this->category) {
            Film::CATEGORY_FILM => $this->getService->getCdnFilmItems($this->page, true),
            Film::CATEGORY_SERIAL => $this->getService->getCdnSerialItems($this->page, true),
            Film::CATEGORY_ANIME => $this->getService->getKodikList(),
        };

        $idsExists = match ($this->category) {
            Film::CATEGORY_FILM, Film::CATEGORY_SERIAL => $this->getImdbExists($items),
            Film::CATEGORY_ANIME => $this->getShikiExists($items),
        };

        $idField = match ($this->category) {
            Film::CATEGORY_FILM, Film::CATEGORY_SERIAL => 'imdb_id',
            Film::CATEGORY_ANIME => 'shikimori_id',
        };

        foreach ($items as $item) {
            if (isset($item->$idField) && $item->$idField) {
                if (in_array($item->$idField, $idsExists)) {
                    if ($this->onlyNew) {
                        return false;
                    } else {
                        continue;
                    }
                }

                match ($this->category) {
                    Film::CATEGORY_FILM => FilmStoreJob::dispatchSync($item->$idField),
                    Film::CATEGORY_SERIAL => SerialStoreJob::dispatchSync($item->$idField),
                    Film::CATEGORY_ANIME => AnimeStoreJob::dispatchSync($item->$idField)
                };

                sleep(5);
            }
        }

        return true;
    }

    private function getImdbExists($items)
    {
        $imdbIds = collect($items)->pluck('imdb_id')->toArray();

        return Film::whereIn('imdb_id', $imdbIds)->pluck('imdb_id')->toArray();
    }

    private function getShikiExists($items)
    {
        $shikiIds = collect($items)->pluck('shikimori_id')->toArray();

        $shikiIds = array_filter($shikiIds, function ($value) {
            return $value !== null;
        });

        return Film::whereIn('shiki_id', $shikiIds)->pluck('shiki_id')->toArray();
    }
}
