<?php

namespace App\Services\Task;

use App\Common\Enums\ExecStatusEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Services\FileService;
use App\Enums\TaskTypeEnum;
use App\Common\Tools\CustomException;
use App\Models\Ks\KsAccountModel;
use App\Services\Ks\KsVideoService;
use Illuminate\Support\Facades\DB;

class TaskKsVideoUploadService extends TaskKsService
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct(TaskTypeEnum::KS_VIDEO_UPLOAD);
    }

    /**
     * @param $taskId
     * @param $data
     * @return bool|void
     * @throws CustomException
     * 创建
     */
    public function createSub($taskId, $data){
        // 验证
        $this->validRule($data, [
            'app_id' => 'required',
            'account_id' => 'required',
            'n8_material_video_id' => 'required',
            'n8_material_video_path' => 'required',
            'n8_material_video_name' => 'required',
            'n8_material_video_signature' => 'required',
        ]);

        $subModel = new $this->subModelClass();
        $subModel->task_id = $taskId;
        $subModel->app_id = $data['app_id'];
        $subModel->account_id = $data['account_id'];
        $subModel->n8_material_video_id = $data['n8_material_video_id'];
        $subModel->n8_material_video_path = $data['n8_material_video_path'];
        $subModel->n8_material_video_name = $data['n8_material_video_name'];
        $subModel->n8_material_video_signature = $data['n8_material_video_signature'];
        $subModel->exec_status = ExecStatusEnum::WAITING;
        $subModel->admin_id = $data['admin_id'] ?? 0;
        $subModel->extends = $data['extends'] ?? [];

        return $subModel->save();
    }

    /**
     * @param $subTask
     * @return bool|void
     * @throws CustomException
     * 执行单个子任务
     */
    public function runSub($subTask){
        // 获取账户信息
        $ksAccountModel = new KsAccountModel();
        $ksAccount = $ksAccountModel->where('app_id', $subTask->app_id)
            ->where('account_id', $subTask->account_id)
            ->first();

        // 获取可推送视频
        $video = $this->getCanPushVideo($subTask->n8_material_video_signature, $ksAccount);

        if(!empty($video)){
            // 推送
            $uploadType = 'push';

            $ssVideoService = new KsVideoService();
            $ssVideoService->pushVideo($ksAccount->access_token,$video->account_id, [$subTask->account_id], [$video->id]);
        }else{
            // 上传
            $uploadType = 'upload';

            // 下载
            $file = (new FileService())->downloadByUrl($subTask->n8_material_video_path);

            // 上传
            $signature =   md5(file_get_contents($file->getRealPath()));
            (new KsVideoService())->uploadVideo($ksAccount->access_token,$subTask->account_id,$signature,$file,['photo_name' => $subTask->n8_material_video_name]);
            // 删除文件
            unlink($file->getRealPath());
        }

        // 上传类型
        $subTask->extends = array_merge($subTask->extends, ['upload_type' => $uploadType]);

        return true;
    }

    /**
     * @param $signature
     * @param $ksAccount
     * @return bool|mixed
     * 获取可推送视频
     */
    public function getCanPushVideo($signature, $ksAccount){
        $enable = StatusEnum::ENABLE;
        $items = DB::select("
            SELECT
                v.id, v.signature, a.account_id
            FROM
                ks_videos v
            LEFT JOIN ks_accounts_videos av ON v.id = av.video_id
            LEFT JOIN ks_accounts a ON av.account_id = a.account_id
            WHERE
                v.signature = '{$signature}'
                AND a.status = '{$enable}'
                AND a.company = '{$ksAccount->company}'
                AND a.user_id != {$ksAccount->user_id}
            LIMIT 1
        ");

        if(empty($items)){
            return false;
        }

        return current($items);
    }
}
