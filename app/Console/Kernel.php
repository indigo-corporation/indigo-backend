<?php

namespace App\Console;

use App\Console\Commands\StoreFilms;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('backup:clean --disable-notifications')->daily()->at('01:00');
        $schedule->command('backup:run --disable-notifications')->daily()->at('01:02');

        $schedule->command('store-films film')->hourly();
        $schedule->command('store-films serial')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
