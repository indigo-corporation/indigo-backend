<?php

namespace App\Console\Commands;

use App\Jobs\AnimeStoreJob;
use App\Models\Film\Film;
use Illuminate\Console\Command;

class StoreTopRatedAnime extends Command
{
    protected $signature = 'store-top-rated-anime';
    protected $description = 'store-top-rated-anime';

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
        for ($p = 1; $p <= 100; $p++) {
            dump('page ' . $p);

            $link = 'https://shikimori.one/api/animes' . '?limit=50&page=' . $p . '&order=popularity';
            $data = json_decode(file_get_contents($link));

            foreach ($data as $item) {
                $shikiIdExists = Film::where('shiki_id', $item->id)->exists();
                if ($shikiIdExists) return false;

                dispatch(new AnimeStoreJob($item->id));
                sleep(10);
            }

            dump('ok');
            sleep(30);
        }
    }
}
