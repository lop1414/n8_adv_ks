<?php

namespace App\Services\Task;

use App\Common\Enums\ExecStatusEnum;
use App\Enums\Ks\KsSyncTypeEnum;
use App\Enums\TaskTypeEnum;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Services\Ks\KsAccountService;
use App\Services\Ks\KsVideoService;

class TaskKsSyncService extends TaskKsService
{
    /**
     * @var
     * 同步类型
     */
    public $syncType;

    /**
     * constructor.
     * @param $syncType
     * @throws CustomException
     */
    public function __construct($syncType)
    {
        parent::__construct(TaskTypeEnum::KS_SYNC);

        // 同步类型
        Functions::hasEnum(KsSyncTypeEnum::class, $syncType);
        $this->syncType = $syncType;
    }

    /**
     * @param $taskId
     * @param $data
     * @return bool
     * @throws CustomException
     * 创建
     */
    public function createSub($taskId, $data){
        // 验证
        $this->validRule($data, [
            'app_id' => 'required',
            'account_id' => 'required',
        ]);

        // 校验
        Functions::hasEnum(KsSyncTypeEnum::class, $this->syncType);

        $subModel = new $this->subModelClass();
        $subModel->task_id = $taskId;
        $subModel->app_id = $data['app_id'];
        $subModel->account_id = $data['account_id'];
        $subModel->sync_type = $this->syncType;
        $subModel->exec_status = ExecStatusEnum::WAITING;
        $subModel->admin_id = $data['admin_id'] ?? 0;
        $subModel->extends = $data['extends'] ?? [];

        return $subModel->save();
    }

    /**
     * @param $taskId
     * @return mixed
     * 获取待执行子任务
     */
    public function getWaitingSubTasks($taskId){
        $subModel = new $this->subModelClass();

        $builder = $subModel->where('task_id', $taskId)
            ->where('sync_type', $this->syncType)
            ->where('exec_status', ExecStatusEnum::WAITING);

        if($this->syncType == KsSyncTypeEnum::VIDEO){
            // 获取3分钟前创建的任务
            $time = time() - 3 * 60;
            $datetime = date('Y-m-d H:i:s', $time);
            $builder->where('created_at', '<', $datetime);
        }

        $subTasks = $builder->orderBy('id', 'asc')->get();

        return $subTasks;
    }

    /**
     * @param $subTask
     * @return bool|void
     * @throws CustomException
     * 执行单个子任务
     */
    public function runSub($subTask){
        if($this->syncType == KsSyncTypeEnum::VIDEO){
            $this->syncVideo($subTask);
        }elseif ($this->syncType == KsSyncTypeEnum::ACCOUNT){
            $this->syncAccount($subTask);

        }else{
            throw new CustomException([
                'code' => 'NOT_HANDLE_FOR_SYNC_TYPE',
                'message' => '该同步类型无对应处理',
            ]);
        }

        return true;
    }

    /**
     * @param $subTask
     * @return bool
     * @throws CustomException
     * 同步视频
     */
    private function syncVideo($subTask){
        $ksVideoService = new KsVideoService($subTask->app_id);

        $option = [
            'account_ids' => [$subTask->account_id],
        ];

        // 筛选视频id
        if(!empty($subTask->extends->video_id)){
            $option['ids'] = [$subTask->extends->video_id];
        }

        $ksVideoService->sync($option);

        return true;
    }


    /**
     * @param $subTask
     * @return bool
     * @throws CustomException
     * 同步账户
     */
    private function syncAccount($subTask){
        $ksAccountService = new KsAccountService($subTask->app_id);

        $option = [
            'user_id' => $subTask->extends->user_id,
        ];
        $ksAccountService->sync($option);
        return true;
    }
}
