<?php

namespace App\Console\Commands;

use App\Models\Film\Film;
use App\Models\Genre\Genre;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;

class PrerenderRoutes extends Command
{
    protected $signature = 'prerender-routes';

    protected $description = 'prerender-routes';


    public function handle()
    {
        $path = '/var/www/indigofilms.online';
        $fileName = 'routes.txt';

        file_put_contents($path . '/' . $fileName, '');

        $fp = fopen($path . '/' . $fileName, 'a+');

        foreach (Film::CATEGORIES as $category) {
            $genreSlugs = Genre::where('is_anime', $category === Film::CATEGORY_ANIME)
                ->pluck('slug')
                ->toArray();

            foreach ($genreSlugs as $slug) {
                $data .= '/' . $category . '/genre/' . $slug . "\r\n";
            }
        }

        fwrite($fp, $data);

        $chunkSize = 250;
        $i = 0;
        Film::with(['translations'])
            ->orderBy('id', 'desc')
            ->chunk($chunkSize, function (Collection $films) use (&$fp, &$i, $chunkSize) {
                $data = '';
                foreach ($films as $film) {
                    $data .= '/' . $film->category . '/' . $film->slug . "\r\n";
                }

                dump(
                    $chunkSize * ++$i
                );

                fwrite($fp, $data);
            });

        fclose($fp);

        $process = Process::forever()
            ->path($path)
            ->start('ng run front-end:prerender --no-guess-routes --routes-file ' . $fileName);

        while ($process->running()) {
            echo $process->latestOutput();
            echo $process->latestErrorOutput();
        }

        $result = $process->wait();
    }
}
