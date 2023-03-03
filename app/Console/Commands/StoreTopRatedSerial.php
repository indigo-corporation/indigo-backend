<?php

namespace App\Console\Commands;

use App\Jobs\SerialStoreJob;
use App\Models\Film\Film;
use App\Services\GetFromUrlService;
use Illuminate\Console\Command;

class StoreTopRatedSerial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store-top-rated-serial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'store-top-rated-serial';

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
        for ($p = 1; $p <= 50; $p++) {
            dump('page ' . $p);

            $url = env('VIDEOCDN_API') . 'tv-series'
                . '?api_token=' . env('VIDEOCDN_TOKEN')
                . '&limit=100&page=' . $p;

            $data = (new GetFromUrlService())->get($url, true);
            $items = $data->data;

            $imdbIds = collect($items)->pluck('imdb_id')->toArray();
            $imdbIdsExists = Film::whereIn('imdb_id', $imdbIds)->pluck('imdb_id')->toArray();

            foreach ($items as $item) {
                if ($item->imdb_id) {
                    if (in_array($item->imdb_id, $imdbIdsExists)) {
                        continue;
                    }

                    dispatch(new SerialStoreJob($item->imdb_id));
                    sleep(10);
                }
            }

            dump('ok');
            sleep(20);
        }
    }
}
