<?php

namespace App\Console\Commands;

use App\Models\Film\Film;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class UpdateAnimeDescription extends Command
{
    protected $signature = 'update-anime-description';

    protected $description = 'update-anime-description';

    public function handle()
    {
        $re = '/(\[.*?\])/m';

        $left = Film::where('category', 'anime')->count();
        $chunkSize = 100;
        $i = 0;
        Film::where('category', 'anime')
            ->with(['translations'])
            ->orderBy('id', 'desc')
            ->chunk($chunkSize, function (Collection $films) use (&$i, $chunkSize, $re, &$left) {
                foreach ($films as $film) {
                    if (!$film->overview) {
                        continue;
                    }
                    $film->overview = preg_replace($re, '', $film->overview);

                    $film->save();
                }

                if (++$i * $chunkSize % 500 === 0) {
                    $left -= 500;

                    dump(
                        $left . ' left'
                    );

                    sleep(1);
                }
            });
    }
}
