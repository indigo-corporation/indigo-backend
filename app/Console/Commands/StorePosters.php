<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StorePosters extends Command
{
    protected $signature = 'store-posters';

    protected $description = 'store-posters';

    public function handle()
    {
        $i = 0;
        DB::table('films')->chunk(100, function (Collection $films) use (&$i) {
            foreach ($films as $film) {
                $film->savePosterThumbs($film->poster);
            }
            dump(++$i * 100);
        });

        dump('completed');
    }
}
