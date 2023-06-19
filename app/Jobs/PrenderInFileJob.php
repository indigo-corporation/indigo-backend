<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;

class PrenderInFileJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        $path = '/var/www/indigofilms.online';
        $fileName = 'routes.txt';

        file_put_contents($path . '/' . $fileName, '');

        $data = '';
        foreach ($this->data as $link) {
            $data .= $link . "\r\n";
        }

        $fp = fopen($path . '/' . $fileName, 'a+');
        fwrite($fp, $data);
        fclose($fp);

        $process = Process::forever()
            ->path($path)
            ->start('ng run front-end:prerender --routes-file ' . $fileName);

        while ($process->running()) {
            echo $process->latestOutput();
            echo $process->latestErrorOutput();
        }

        $result = $process->wait();
    }
}
