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
    protected $signature = 'store-films {category=film} {page=1}';

    protected $description = 'store-films';

    private string $category;
    private int $page;

    public function handle()
    {
        $this->category = $this->argument('category');
        $this->page = (int)$this->argument('page');

        if (!in_array($this->category, Film::CATEGORIES)) {
            throw new \Error('wrong category');
        }

        if ($this->page <= 0) {
            throw new \Error('wrong page');
        }

        for ($p = $this->page; $p <= 50; $p++) {
            $this->page = $p;
            dump('=== page ===');
            dump($p);
            dump('------------');

            try {
                $this->processPage();
            } catch (\Throwable $e) {
                dd($e->getMessage());
            }

            dump('ok');
            sleep(20);
        }
    }

    private function processPage()
    {
        $items = match ($this->category) {
            Film::CATEGORY_FILM => $this->getFilmItems(),
            Film::CATEGORY_SERIAL => $this->getSerialItems(),
            Film::CATEGORY_ANIME => $this->getAnimeItems(),
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

                sleep(10);
            }
        }
    }

    private function getFilmItems()
    {
        $url = env('VIDEOCDN_API') . 'movies'
            . '?api_token=' . env('VIDEOCDN_TOKEN')
            . '&limit=100&page=' . $this->page;

        $data = (new GetFromUrlService())->get($url, true);

        return $data->data;
    }

    private function getSerialItems()
    {
        $url = env('VIDEOCDN_API') . 'tv-series'
            . '?api_token=' . env('VIDEOCDN_TOKEN')
            . '&limit=100&page=' . $this->page;

        $data = (new GetFromUrlService())->get($url, true);

        return $data->data;
    }

    private function getAnimeItems()
    {
        $url = 'https://shikimori.one/api/animes' . '?limit=50&page=' . $this->page . '&order=popularity';

        return (new GetFromUrlService())->get($url, true);
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
