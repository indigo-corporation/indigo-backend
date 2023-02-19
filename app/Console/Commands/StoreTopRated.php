<?php

namespace App\Console\Commands;

use App\Jobs\FilmStoreJob;
use App\Models\Film\Film;
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

            $link = env('VIDEOCDN_API') . 'movies'
                . '?api_token=' . env('VIDEOCDN_TOKEN')
                . '&limit=100&page=' . $p;

            try {
                $data = json_decode(file_get_contents($link));

                foreach ($data->data as $item) {
                    if ($item->imdb_id) {
                        $imdbIdExists = Film::where('imdb_id', $item->imdb_id)->exists();
                        if ($imdbIdExists) continue;

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
