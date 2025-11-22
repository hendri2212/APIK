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
        // $schedule->command('inspire')->hourly();

        // Otomatis Checkout antara 16.00 - 23.00 (Misalnya dijalankan jam 16.30)
        $schedule->command('absen:auto-checkout')->weekdays()->at('16:30');

        // CheckIn otomatis jam 07.20
        $schedule->command('absen:auto-checkin')->weekdays()->at('07:20');
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
