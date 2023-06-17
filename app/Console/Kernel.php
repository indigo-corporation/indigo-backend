<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('store-films film 1 10')->daily()->at('00:50');
        $schedule->command('store-films serial 1 10')->daily()->at('00:50');

        $schedule->command('backup:clean --disable-notifications')->daily()->at('01:00');
        $schedule->command('backup:run --only-db --disable-notifications')->daily()->at('01:02');

        $schedule->command('store-films film 1 3')->hourly();
        $schedule->command('store-films serial 1 3')->hourly();

        $schedule->command('horizon:snapshot')->everyFiveMinutes();
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
