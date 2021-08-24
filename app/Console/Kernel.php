<?php

namespace App\Console;

use App\Console\Commands\Ks\KsSyncVideoCommand;
use App\Console\Commands\Ks\Report\KsSyncAccountReportCommand;
use App\Console\Commands\Ks\Report\KsSyncCreativeReportCommand;
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
        ReloadKsAccountCommand::class,

        // 同步
        KsSyncVideoCommand::class,

        // 任务
        TaskKsSyncCommand::class,
        TaskKsVideoUploadCommand::class,

        // 快手
        SyncKsAccountCommand::class,
        KsSyncAccountReportCommand::class,
        KsSyncCreativeReportCommand::class,
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

        // 快手账户报表同步
        $schedule->command('ks:sync_account_report --date=today')->cron('*/2 * * * *');
        $schedule->command('ks:sync_account_report --date=yesterday --key_suffix=yesterday')->cron('15-20 11 * * *');

        // 快手创意报表同步
        $schedule->command('ks:sync_creative_report --date=today --run_by_account_charge=1 --multi_chunk_size=3')->cron('*/2 * * * *');
        $schedule->command('ks:sync_creative_report --date=yesterday --key_suffix=yesterday')->cron('10-15 10,15 * * *');
    }
}
