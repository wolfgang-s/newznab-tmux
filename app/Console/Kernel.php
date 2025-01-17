<?php

namespace App\Console;

use App\Jobs\RemoveInactiveAccounts;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('disposable:update')->weekly();
        $schedule->command('clean:directories')->hourly();
        $schedule->command('nntmux:delete-unverified-users')->twiceDaily(1, 13);
        $schedule->command('nntmux:update-expired-roles')->daily();
        $schedule->command('nntmux:remove-bad')->hourly();
        $schedule->command('telescope:prune')->daily();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
        $schedule->command('cloudflare:reload')->daily();
        if (config('nntmux.purge_inactive_users') === true) {
            $schedule->job(new RemoveInactiveAccounts())->daily();
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
