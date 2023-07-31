<?php

namespace App\Console\Commands;

use App\Models\Country;
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

        $data = '';

        foreach (Film::CATEGORIES as $category) {
            $genres = Genre::getList($category === Film::CATEGORY_ANIME);

            foreach ($genres as $genre) {
                $data .= '/' . $category . '/genre/' . $genre->slug . "\r\n";
            }
        }

        $countries = Country::getList();
        foreach (Film::CATEGORIES as $category) {
            if ($category === Film::CATEGORY_ANIME) {
                continue;
            }

            foreach ($countries as $country) {
                $data .= '/' . $category . '/country/' . $country->slug . "\r\n";
            }
        }

        foreach (Film::CATEGORIES as $category) {
            for ($year = 1910; $year <= date('Y'); $year++) {
                $data .= '/' . $category . '/year/' . $year . "\r\n";
            }
        }

        fwrite($fp, $data);

        $chunkSize = 250;
        $i = 0;
        Film::with(['translations'])
            ->where('is_hidden', false)
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

        //        $process = Process::forever()
        //            ->path($path)
        //            ->start('ng run front-end:prerender --no-guess-routes --routes-file ' . $fileName);
        //
        //        while ($process->running()) {
        //            echo $process->latestOutput();
        //            echo $process->latestErrorOutput();
        //        }
        //
        //        $process->wait();
    }
}
