<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use App\Console\Commands\AutoSaveForgeProperties;
use App\Console\Commands\AutoSaveForgeProject;
use App\Console\Commands\AutoBackup;

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
        try{
        // $schedule->command('inspire')
        //          ->hourly();
        //$schedule->command('forge:save_properties')->cron('6 * * * *');;
        $schedule->command('forge:auto_backup')->weeklyOn(5,'22:00');//('15:00');//everyMinute();  
        $schedule->command('forge:save')
                    //->appendOutputTo('/var/www/html/iPD/public/schedule_error/error.log')
                    ->weeklyOn(6,'9:00');//dailyAt('19:00');
        $schedule->command('forge:save_properties')
                //->appendOutputTo('/var/www/html/iPD/public/schedule_error/error.log')
                ->weeklyOn(7,'9:00');//dailyAt('19:00');
        
        //$schedule->command('forge:auto_backup')->weeklyOn(6, '19:00');//dailyAt('19:00');//everyMinute();
        //$schedule->command('forge:save')->weeklyOn(5, '19:00');//('15:00');//everyMinute();
          //$schedule->command('forge:save_properties')->weeklyOn(6, '9:00');//dailyAt('19:00');//everyMinute();
          //$schedule->command('forge:room_properties')->weeklyOn(7, '9:00');//dailyAt('19:00');//everyMinute();
        }catch(Exception $e){
            $e->getMessage();
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
