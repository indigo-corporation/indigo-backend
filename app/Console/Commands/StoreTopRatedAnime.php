<?php

namespace App\Console\Commands;

use App\Jobs\AnimeStoreJob;
use App\Models\Film\Film;
use Illuminate\Console\Command;

class StoreTopRatedAnime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store-top-rated-anime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'store-top-rated-anime';

    /**
     * Create a new command instance.
     *
     * @return void
     */
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
        Film::where('is_anime', true)->delete();

        for ($p = 1; $p <= 10; $p++) {
            dump('page ' . $p);
            $link = 'https://shikimori.one/api/animes'
                . '?limit=50&page=' . $p
                . '&order=popularity';

            $data = json_decode(file_get_contents($link));

            foreach ($data as $item) {
                $link = 'https://shikimori.one/api/animes/' . $item->id;
                $data = json_decode(file_get_contents($link));

                try {
                    dispatch(new AnimeStoreJob($data));
                } catch (\Throwable $e) {
                    dd($e->getMessage());
                }
            }

            dump('ok');
            sleep(60);
        }
    }
}
