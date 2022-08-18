<?php

namespace App\Console\Commands;

use App\Jobs\FilmStoreJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StoreTopRated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store-top-rated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'store-top-rated';

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
//        DB::table('films')->truncate();

        for ($p = 1; $p <= 25; $p++) {
            dump('page ' . $p);

            $link = env('TMDB_API') . 'movie/top_rated'
                . '?api_key=' . env('TMDB_KEY')
                . '&language=ru&page=' . $p;

            try {
                $data = json_decode(file_get_contents($link));

                foreach ($data->results as $item) {
                    dispatch(new FilmStoreJob($item));
                }
            } catch (\Throwable $e) {
                dd($e->getMessage());
            }
        }
    }
}
