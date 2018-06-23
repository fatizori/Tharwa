<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by the application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ExecuteExterneTransfer::class,
        \App\Console\Commands\ExecuteMensuelleCommissions::class,
];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('execute:externe_transfer')->dailyAt('00:00s');
        $schedule->command('execute:mensuelle_commissions')->dailyAt('08:00')->when(function () {
            return \Carbon\Carbon::now()->endOfMonth()->isToday();
        });
    }
}
