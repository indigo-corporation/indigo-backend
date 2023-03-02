<?php

namespace App\Console\Commands;

use App\Jobs\AnimeStoreJob;
use App\Jobs\SerialStoreJob;
use App\Models\Film\Film;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

            $link = env('VIDEOCDN_API') . 'tv-series'
                . '?api_token=' . env('VIDEOCDN_TOKEN')
                . '&limit=100&page=' . $p;

            $data = json_decode(file_get_contents($link));

            $imdbIds = collect($data->data)->pluck('imdb_id')->toArray();
            $imdbIdsExists = Film::whereIn('imdb_id', $imdbIds)->pluck('imdb_id')->toArray();

            foreach ($data->data as $item) {
                if ($item->imdb_id) {
                    if (in_array($item->imdb_id, $imdbIdsExists)) continue;

                    dispatch(new SerialStoreJob($item->imdb_id));
                    sleep(10);
                }
            }

            dump('ok');
            sleep(20);
        }
    }
}
