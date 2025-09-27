<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\User;
use App\Tour;
use App\TourPackage;
use App\Notification;
use App\Status;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Setting;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // '\App\Console\Commands\Inspire',
        '\App\Console\Commands\UsersTourNotification',
        '\App\Console\Commands\TourPackageNotification',
        '\App\Console\Commands\ParseEmails',
        '\App\Console\Commands\ParseRequest',
        '\App\Console\Commands\TaskDeadlineNotification',
		'\App\Console\Commands\RunImport',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('checktourpackage:status')->daily();

//        $schedule->command('email:parse 10 --period_type=M --check_frequency')->everyMinute()
//	        ->appendOutputTo(storage_path('logs/email-parse.log'))
//        ;
	    $schedule->command('email:parse')->everyMinute()
	             ->appendOutputTo(storage_path('logs/email-parse-new.log'))
	    ;
//	    $schedule->command('parse:request')->everyMinute()
//	             ->appendOutputTo(storage_path('logs/email-parse.log'))
//	    ;

        $schedule->command('season:import')->everyMinute()
            ->appendOutputTo(storage_path('logs/season-import.log'))
        ;

	    $schedule->command('task:deadline')->daily();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
