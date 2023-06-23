<?php

namespace App\Console\Commands;

use App\Jobs\PrenderInFileJob;
use App\Models\Film\Film;
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
        $data = '';

        file_put_contents($path . '/' . $fileName, $data);

        $chunkSize = 250;
        Film::with(['translations'])
            ->orderBy('id', 'desc')
            ->chunk($chunkSize, function (Collection $films) use ($path, $fileName) {
                $data = '';
                foreach ($films as $film) {
                    $data = '/' . $film->category . '/' . $film->slug . "\r\n";
                }

                $fp = fopen($path . '/' . $fileName, 'a+');
                fwrite($fp, $data);
                fclose($fp);
            });

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
