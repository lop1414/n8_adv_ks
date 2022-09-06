<?php

namespace App\Console;

use App\Common\Console\ConvertCallbackCommand;
use App\Common\Console\Monitor\QueueClickMonitorCommand;
use App\Common\Console\Queue\QueueClickCommand;
use App\Common\Helpers\Functions;
use App\Console\Commands\CleanInvalidClickDataCommand;
use App\Console\Commands\Ks\KsRefreshAccessTokenCommand;
use App\Console\Commands\Ks\KsSyncCommand;
use App\Console\Commands\Ks\KsSyncVideoCommand;
use App\Console\Commands\Ks\Report\KsSyncAccountReportCommand;
use App\Console\Commands\Ks\Report\KsSyncCreativeReportCommand;
use App\Console\Commands\Ks\Report\KsSyncMaterialReportCommand;
use App\Console\Commands\Ks\Report\KsSyncProgramCreativeReportCommand;
use App\Console\Commands\MaterialCreativeCommand;
use App\Console\Commands\SyncChannelUnitCommand;
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

        // 队列
        QueueClickCommand::class,
        // 监控
        QueueClickMonitorCommand::class,


        // 转化回传
        ConvertCallbackCommand::class,

        // 同步
        KsSyncVideoCommand::class,
        KsSyncCommand::class,

        // 任务
        TaskKsSyncCommand::class,
        TaskKsVideoUploadCommand::class,

        // 快手
        KsSyncAccountReportCommand::class,
        KsSyncCreativeReportCommand::class,
        KsSyncProgramCreativeReportCommand::class,
        KsSyncMaterialReportCommand::class,
        KsRefreshAccessTokenCommand::class,

        // 同步渠道-广告组关联
        SyncChannelUnitCommand::class,

        // 分析素材创意关联
        MaterialCreativeCommand::class,

        // 清除无效点击
        CleanInvalidClickDataCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 队列
        $schedule->command('queue:click')->cron('* * * * *');

        // 转化上报
        $schedule->command('convert_callback')->cron('* * * * *');

        // 同步渠道-广告组
        $schedule->command('sync_channel_unit --date=today')->cron('*/2 * * * *');

        // 同步任务
        $schedule->command('ks:sync --type=campaign --date=today')->cron('*/20 * * * *');
        $schedule->command('ks:sync --type=unit --date=today')->cron('*/20 * * * *');
        $schedule->command('ks:sync --type=creative --date=today')->cron('*/20 * * * *');
        $schedule->command('ks:sync --type=program_creative --date=today')->cron('*/20 * * * *');
        $schedule->command('ks:sync_video --date=today')->cron('*/30 * * * *');

        // 任务
        $schedule->command('task:ks_video_upload')->cron('* * * * *');
//        $schedule->command('task:ks_sync --type=video')->cron('* * * * *');

        // 快手账户报表同步
        $schedule->command('ks:sync_account_report --has_history_cost=1 --date=today')->cron('*/2 * * * *');
        $schedule->command('ks:sync_account_report --date=yesterday --key_suffix=yesterday')->cron('15-20 11 * * *');

        // 快手程序化创意报表同步
        $schedule->command('ks:sync_program_creative_report --date=today --run_by_account_charge=1 --multi_chunk_size=3')->cron('*/2 * * * *');
        $schedule->command('ks:sync_program_creative_report --date=yesterday --key_suffix=yesterday')->cron('10-15 10,15 * * *');
        // 快手自定义创意报表同步
        $schedule->command('ks:sync_creative_report --date=today --run_by_account_charge=1 --multi_chunk_size=3')->cron('*/2 * * * *');
        $schedule->command('ks:sync_creative_report --date=yesterday --key_suffix=yesterday')->cron('10-15 10,15 * * *');

        // 快手素材报表
        $schedule->command('ks:sync_material_report --date=today --run_by_account_charge=1 --multi_chunk_size=3')->cron('*/10 * * * *');
        $schedule->command('ks:sync_material_report --date=yesterday')->cron('10 9,15 * * *');

        //分析素材创意关联
        $schedule->command('material_creative --date=today')->cron('*/10 * * * *');
        $schedule->command('material_creative --date=yesterday')->cron('10-15 10 * * *');

        // 正式
        if(Functions::isProduction()){
            // 刷新 access_token
            $schedule->command('ks:refresh_access_token')->cron('0 */8 * * *');

            // 监控
            $schedule->command('monitor:click_queue')->cron('* * * * *');

//            $schedule->command('clean_invalid_click_data  --date=today')->cron('* 1 * * *');
        }
    }
}
