<?php

namespace App\Console\Commands;

use App\Jobs\AnimeStoreJob;
use App\Models\Film\Film;
use App\Services\GetFromUrlService;
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

            $url = 'https://shikimori.one/api/animes' . '?limit=50&page=' . $p . '&order=popularity';
            $items = (new GetFromUrlService())->get($url, true);

            $shikiIds = collect($items)->pluck('id')->toArray();
            $shikiIdsExists = Film::whereIn('shiki_id', $shikiIds)->pluck('shiki_id')->toArray();

            foreach ($items as $item) {
                if (in_array($item->id, $shikiIdsExists)) {
                    continue;
                }

                dispatch(new AnimeStoreJob($item->id));
                sleep(10);
            }

            dump('ok');
            sleep(30);
        }
    }
}
