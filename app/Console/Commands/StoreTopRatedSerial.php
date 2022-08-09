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
        DB::table('films')->truncate();

        for ($p = 1; $p <= 5; $p++) {
            dump('page ' . $p);

            $link = 'https://videocdn.tv/api/tv-series'
                . '?api_token=mkCYL7WFzktgIqXJ8UTgVr2lZ5ZJknFX'
                . '&limit=100&page=' . $p;

            try {
                $data = json_decode(file_get_contents($link));

                foreach ($data->data as $item) {
                    dispatch(new SerialStoreJob($item));
                }
            } catch (\Throwable $e) {
                dd($e->getMessage());
            }
        }
    }
}
