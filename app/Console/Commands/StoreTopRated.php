<?php

namespace App\Console\Commands;

use App\Jobs\FilmStoreJob;
use App\Models\Film\Film;
use App\Services\GetFromUrlService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StoreTopRated extends Command
{
    protected $signature = 'store-top-rated';

    protected $description = 'store-top-rated';

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
//        DB::table('films')->truncate();

        for ($p = 1; $p <= 50; $p++) {
            dump('page ' . $p);

            $url = env('VIDEOCDN_API') . 'movies'
                . '?api_token=' . env('VIDEOCDN_TOKEN')
                . '&limit=100&page=' . $p;

            try {
                $data = (new GetFromUrlService())->get($url, true);
                $items = $data->data;

                $imdbIds = collect($items)->pluck('imdb_id')->toArray();
                $imdbIdsExists = Film::whereIn('imdb_id', $imdbIds)->pluck('imdb_id')->toArray();

                foreach ($items as $item) {
                    if ($item->imdb_id) {
                        if (in_array($item->imdb_id, $imdbIdsExists)) continue;

                        dispatch(new FilmStoreJob($item->imdb_id));
                        sleep(10);
                    }
                }
            } catch (\Throwable $e) {
                dd($e->getMessage());
            }

            dump('ok');
            sleep(20);
        }
    }
}
