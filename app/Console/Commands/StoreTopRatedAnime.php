<?php

namespace App\Console\Commands;

use App\Jobs\AnimeStoreJob;
use App\Models\Film\Film;
use Illuminate\Console\Command;

class StoreTopRatedAnime extends Command
{
    protected $signature = 'store-top-rated-anime';
    protected $description = 'store-top-rated-anime';

    private $shikiId = null;
    private $imdbId = null;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        for ($p = 1; $p <= 30; $p++) {
            dump('page ' . $p);

            $link = 'https://shikimori.one/api/animes' . '?limit=50&page=' . $p . '&order=popularity';
            $data = json_decode(file_get_contents($link));

            foreach ($data as $item) {
                $this->shikiId = $item->id;

                if (!$this->needToStore()) continue;

                dispatch(new AnimeStoreJob($this->shikiId, $this->imdbId));
                sleep(10);
            }

            dump('ok');
            sleep(30);
        }
    }

    private function needToStore(): bool
    {
        $shikiIdExists = Film::where('shiki_id', $this->shikiId)->exists();
        if ($shikiIdExists) return false;

        $kodikUrl = env('KODIK_API') . 'search?token=' . env('KODIK_TOKEN') . '&shikimori_id=' . $this->shikiId;
        $kodikData = json_decode(file_get_contents($kodikUrl))->results;
        if (!$kodikData) return false;

        $this->imdbId = (($kodikData[0])->imdb_id);

        if ($this->imdbId) {
            $imdbIdExists = Film::where('imdb_id', $this->imdbId)->exists();
            if ($imdbIdExists) return false;
        }

        return true;
    }
}
