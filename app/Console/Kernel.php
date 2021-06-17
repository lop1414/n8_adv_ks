<?php

namespace App\Console;

use App\Console\Commands\Ks\KsSyncVideoCommand;
use App\Console\Commands\SecondVersion\ReloadKsAccountCommand;
use App\Console\Commands\SecondVersion\SyncKsAccountCommand;
use App\Console\Commands\Task\TaskKsSyncCommand;
use App\Console\Commands\Task\TaskKsVideoUploadCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // 二版
        SyncKsAccountCommand::class,
        ReloadKsAccountCommand::class,

        // 同步
        KsSyncVideoCommand::class,

        // 任务
        TaskKsSyncCommand::class,
        TaskKsVideoUploadCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 二版
        $schedule->command('second_version:sync_ks_account')->cron('5 * * * *');
        $schedule->command('second_version:reload_ks_account')->cron('* * * * *');

        // 任务
        $schedule->command('task:ks_video_upload')->cron('* * * * *');
        $schedule->command('task:ks_sync --type=video')->cron('* * * * *');
    }
}
