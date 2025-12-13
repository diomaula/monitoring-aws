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
        /**
         * 1️⃣ Fetch RAW data AWS
         * Jalan setiap menit
         * Data disimpan hanya pada menit 55–59 (logika di command)
         */
        $schedule->command('aws:fetch-raw')
            ->everyMinute()
            ->timezone('UTC')
            ->withoutOverlapping()
            ->runInBackground();

        /**
         * 2️⃣ Generate laporan harian
         * Ambil data kemarin (UTC)
         */
        $schedule->command('report:daily')
            ->dailyAt('00:10')
            ->timezone('UTC')
            ->withoutOverlapping();

        /**
         * 3️⃣ Export bulanan (tidak diubah)
         */
        $schedule->command('aws:export')
            ->monthlyOn(1, '00:10')
            ->timezone('UTC')
            ->withoutOverlapping();

        /**
         * 4️⃣ Cek status AWS (monitoring real-time)
         */
        $schedule->command('app:check-aws-status')
            ->everyMinute()
            ->timezone('UTC')
            ->withoutOverlapping();
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
