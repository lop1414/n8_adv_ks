<?php

namespace App\Console\Commands\Task;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Enums\Ks\KsSyncTypeEnum;
use App\Services\Task\TaskKsSyncService;

class TaskKsSyncCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'task:ks_sync {--type=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '快手同步任务';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * @throws \App\Common\Tools\CustomException
     * 处理
     */
    public function handle(){
        $type = strtoupper($this->option('type'));
        Functions::hasEnum(KsSyncTypeEnum::class, $type);

        $taskKsSyncService = new TaskKsSyncService($type);
        $option = ['log' => true];
        $this->lockRun(
            [$taskKsSyncService, 'run'],
            "task_ks_sync_{$type}",
            43200,
            $option
        );
    }
}
