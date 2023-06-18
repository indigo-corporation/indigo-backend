<?php

namespace App\Console\Commands;

use App\Models\Film\Film;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class CreateRoutesFile extends Command
{
    protected $signature = 'create-routes-file';

    protected $description = 'create-routes-file';


    public function handle()
    {
        $fileName = '/var/www/indigofilms.online/routes.txt';

        file_put_contents($fileName, '');

        $i = 0;
        Film::with(['translations'])
            ->orderBy('id', 'desc')
            ->chunk(1000, function (Collection $films) use (&$i, $fileName) {
                $data = '';

                foreach ($films as $film) {
                    $data .= '/' . $film->category . '/' . $film->slug . "\r\n";
                }

                $fp = fopen($fileName, 'a+');
                fwrite($fp, $data);
                fclose($fp);

                dump('processed ' . ++$i * 1000);
            });
    }
}
