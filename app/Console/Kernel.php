<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\JamAbsen;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $jamAbsen = JamAbsen::first();

        $checkinTime  = $jamAbsen->checkin_time  ?? '07:20:00';
        $checkoutTime = $jamAbsen->checkout_time ?? '16:30:00';

        $schedule->command('absen:auto-checkin')
            ->weekdays()
            ->at(substr($checkinTime, 0, 5)); // jadi '07:20'

        $schedule->command('absen:auto-checkout')
            ->weekdays()
            ->at(substr($checkoutTime, 0, 5)); // jadi '16:30'
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
