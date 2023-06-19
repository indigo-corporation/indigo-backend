<?php

namespace App\Console\Commands;

use App\Models\Film\Film;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;

class CreateRoutesFile extends Command
{
        protected $signature = 'create-routes-file';

    protected $description = 'create-routes-file';


    public function handle()
    {
        $path = '/var/www/indigofilms.online';
        $fileName = 'routes.txt';

//        $fp = fopen($path . '/' . $fileName, 'a+');
//        fwrite($fp, implode("\r\n", [
//            '/',
//            '/film',
//            '/anime',
//            '/serial',
//            '/cartoon',
//            '/search-page',
//            '/support',
//            '/copyright',
//            '/send-reset-password',
//            '/404'
//        ]));
//        fclose($fp);

        $chunkSize = 150;
        $i = 0;
        Film::with(['translations'])
            ->orderBy('id', 'desc')
            ->chunk($chunkSize, function (Collection $films) use (&$i, $chunkSize, $path, $fileName) {
                file_put_contents($path . '/' . $fileName, '');

                dump('start processing ' . $chunkSize);
                $timeStart = now();
                $data = '';

                foreach ($films as $film) {
                    $data .= '/' . $film->category . '/' . $film->slug . "\r\n";
                }

                $fp = fopen($path . '/' . $fileName, 'a+');
                fwrite($fp, $data);
                fclose($fp);

                $process = Process::forever()
                    ->path($path)
                    ->start('ng run front-end:prerender --routes-file ' . $fileName);

                while ($process->running()) {
//                    echo $process->latestOutput();
                    echo $process->latestErrorOutput();
                }

                $result = $process->wait();

//                echo $result->output();
//                echo $result->errorOutput();

                $timeEnd = now();
                dump('processed in ' . strtotime($timeEnd) - strtotime($timeStart));
                dump('processed ' . ++$i * $chunkSize);

                sleep(30);
            });
    }
}
