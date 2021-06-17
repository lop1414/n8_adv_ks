<?php

namespace App\Console\Commands\Task;

use App\Common\Console\BaseCommand;
use App\Services\Task\TaskKsVideoUploadService;

class TaskKsVideoUploadCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'task:ks_video_upload';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '快手视频上传任务';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * 处理
     */
    public function handle(){
        $taskKsVideoUploadService = new TaskKsVideoUploadService();
        $option = ['log' => true];
        $this->lockRun(
            [$taskKsVideoUploadService, 'run'],
            'task_ks_video_upload',
            43200,
            $option
        );
    }
}
