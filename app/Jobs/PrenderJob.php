<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;

class PrenderJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private string $route;

    public function __construct(string $route)
    {
        $this->route = $route;
    }

    public function handle()
    {
        Process::forever()
            ->path('/var/www/indigofilms.online')
            ->run('ng run front-end:prerender --no-guess-routes --routes ' . $this->route);
    }
}
