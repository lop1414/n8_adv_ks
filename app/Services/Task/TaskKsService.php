<?php

namespace App\Services\Task;

use App\Common\Enums\ExecStatusEnum;
use App\Common\Enums\TaskStatusEnum;
use App\Common\Models\TaskModel;
use App\Common\Services\TaskService;
use App\Common\Tools\CustomException;
use App\Services\Ks\KsService;

class TaskKsService extends TaskService
{
    /**
     * @var KsService
     */
    public $ksService;

    /**
     * constructor.
     * @param $taskType
     * @throws CustomException
     */
    public function __construct($taskType)
    {
        parent::__construct($taskType);

        $this->ksService = new KsService();
    }

    /**
     * @throws CustomException
     * 重执行
     */
    public function reWaiting(){
        $createdAt = date('Y-m-d H:i:s', (time() - 86400 * 7));
        $taskStatus = TaskStatusEnum::DONE;
        $execStatus = ExecStatusEnum::FAIL;
        $taskType = $this->taskType;

        if(empty($this->subModelClass)){
            throw new CustomException([
                'code' => 'PLEASE_SET_CONSTRUCT_SUB_MODEL_CLASS',
                'message' => '请设置构造子模型类',
            ]);
        }

        $subModel = new $this->subModelClass;
        $failSubTasks = $subModel->whereRaw("
                task_id IN (
                    SELECT id FROM tasks 
                        WHERE created_at >= '$createdAt'
                        AND task_status = '{$taskStatus}'
                        AND task_type = '{$taskType}'
                ) AND exec_status = '{$execStatus}'
            ")->get();

        foreach($failSubTasks as $failSubTask){
            if(empty($failSubTask->fail_data)){
                continue;
            }

            $failResult = $failSubTask->fail_data['data']['result'] ?? [];
            if($this->ksService->sdk->isNetworkError($failResult)){
                // 网络错误
                $this->updateReWaitingStatus($failSubTask);
            }
        }
    }

    /**
     * @param $subTask
     * @return bool
     * @throws CustomException
     * 更新重执行状态
     */
    public function updateReWaitingStatus($subTask){
        $subTask->exec_status = ExecStatusEnum::WAITING;
        $subTask->save();

        $task = TaskModel::find($subTask->task_id);
        $this->updateTaskStatus($task, TaskStatusEnum::WAITING);

        return true;
    }
}
