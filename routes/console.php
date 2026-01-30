<?php

namespace App\Console;


use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CalculateOverdueFines;
use App\Console\Commands\LibraryStats;
use App\Console\Commands\RegenerateQRCodes;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CalculateOverdueFines::class,
        \App\Console\Commands\LibraryStats::class,
        \App\Console\Commands\RegenerateQRCodes::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Calculate overdue fines every day at 00:01 AM
        $schedule->command('fines:calculate-overdue')
            ->dailyAt('00:01')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/scheduler.log'));

        // Update existing fines every 6 hours
        $schedule->command('fines:calculate-overdue --update')
            ->everySixHours()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/scheduler.log'));

        // Mark expired bookings every hour
        $schedule->call(function () {
            app(\App\Services\BookingService::class)->markExpiredBookings();
        })->hourly();

        // Send reminder emails for books due tomorrow (if email feature implemented)
        // $schedule->command('borrowings:send-reminders')
        //     ->dailyAt('08:00')
        //     ->appendOutputTo(storage_path('logs/scheduler.log'));

        // Cleanup old logs (optional)
        $schedule->command('telescope:prune --hours=48')
            ->dailyAt('02:00')
            ->when(function () {
                return class_exists('Laravel\Telescope\Telescope');
            });
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
