<?php

namespace App\Console;

use App\Jobs\CallExternalApiJob;
use App\Jobs\DeletedOldPostsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new DeletedOldPostsJob())->daily();
        $schedule->job(new CallExternalApiJob())->everySixHours()
            ->sendOutputTo(storage_path('logs/DailYCallJob.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
