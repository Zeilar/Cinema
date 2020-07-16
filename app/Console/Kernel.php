<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Cache;
use App\User;

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
        $schedule->call(function() {
            // Remove old data, the server is not that large
            $oldUsers = \App\User::where('created_at', '<', \Carbon\Carbon::now()->subDays(1))->get();
            $oldUsers->each(function($user) {
                $user->delete();
            });
            $oldVideos = \App\Video::where('created_at', '<', \Carbon\Carbon::now()->subDays(1))->get();
            $oldVideos->each(function($video) {
                $video->delete();
            });
        })->everyMinute();
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
