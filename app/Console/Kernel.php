<?php

namespace App\Console;

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
        \App\Console\Commands\AdminPassword::class,
        \App\Console\Commands\GenerateInvoicesForAllCustomers::class,
        \App\Console\Commands\DeactivateExpiredProducts::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Schedule automatic deactivation of expired products daily
        $schedule->command('products:deactivate-expired')->daily();
        
        // Also schedule invoice generation if needed
        $schedule->command('invoices:generate-all')->monthly();
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
