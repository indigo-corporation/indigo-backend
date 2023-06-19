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

        file_put_contents($path . '/' . $fileName, '');

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

        $i = 0;
        Film::with(['translations'])
            ->orderBy('id', 'desc')
            ->chunk(10, function (Collection $films) use (&$i, $path, $fileName) {
                $data = '';

                foreach ($films as $film) {
                    $data .= '/' . $film->category . '/' . $film->slug . "\r\n";
                }

                $fp = fopen($path . '/' . $fileName, 'a+');
                fwrite($fp, $data);
                fclose($fp);

                $result = Process::forever()
                    ->path($path)
                    ->run('ng run front-end:prerender --routes-file ' . $fileName);

                echo $result->output();
                echo $result->errorOutput();

                dump('processed ' . ++$i * 100);
            });


    }
}
