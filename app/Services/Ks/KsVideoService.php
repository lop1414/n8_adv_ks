<?php

namespace App\Services\Ks;

use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Enums\Ks\KsSyncTypeEnum;
use App\Models\Ks\KsAccountVideoModel;
use App\Models\Ks\KsVideoModel;
use App\Services\Task\TaskKsSyncService;

class KsVideoService extends KsService
{
    /**
     * constructor.
     * @param string $appId
     */
    public function __construct($appId = ''){
        parent::__construct($appId);
    }

    /**
     * @param $accountId
     * @param $signature
     * @param $file
     * @param array $param
     * @return mixed
     * @throws CustomException
     * 上传视频
     */
    public function uploadVideo($accountId, $signature, $file, $param = []){
        $this->setAccessToken();

        $ret = $this->sdk->uploadVideo($accountId, $signature, $file, $param);
        Functions::consoleDump($ret);

        // 同步
        if(!empty($ret['photo_id'])){
//            $taskKsSyncService = new TaskKsSyncService(KsSyncTypeEnum::VIDEO);
//            $task = [
//                'name' => '同步快手视频',
//                'admin_id' => 0,
//            ];
//            $subs = [];
//            $subs[] = [
//                'app_id' => $this->sdk->getAppId(),
//                'account_id' => $accountId,
//                'admin_id' => 0,
//                'extends' => [
//                    'video_id' => $ret['photo_id']
//                ],
//            ];
//            $taskKsSyncService->create($task, $subs);
        }

        return $ret;
    }

    /**
     * @param $accountId
     * @param array $photoIds
     * @param array $targetAccountIds
     * @return mixed
     * @throws CustomException
     * 推送视频
     */
    public function pushVideo($accountId, array $targetAccountIds, array $photoIds){
        $this->setAccessToken();

        return $this->sdk->pushVideo($accountId, $targetAccountIds, $photoIds);
    }

    /**
     * @param $accounts
     * @param $page
     * @param $pageSize
     * @param array $param
     * @return mixed
     * sdk并发获取列表
     */
    public function sdkMultiGetList($accounts, $page, $pageSize, $param = []){
        return $this->sdk->multiGetVideoList($accounts, $page, $pageSize, $param);
    }

    /**
     * @param array $option
     * @return bool
     * @throws CustomException
     * 同步
     */
    public function sync($option = []){
        ini_set('memory_limit', '2048M');

        $accountIds = [];
        // 账户id过滤
        if(!empty($option['account_ids'])){
            $accountIds = $option['account_ids'];
        }

        $param = [];
        if(!empty($option['date'])){
            $param['start_time'] = Functions::getDate($option['date']);
            $param['end_time'] = Functions::getDate($option['date']);
        }

        if(!empty($option['ids'])){
            $param['photo_ids'] = $option['ids'];
        }

        $accountGroup = $this->getAccountGroup($accountIds);

        $t = microtime(1);

        $pageSize = 100;
        foreach($accountGroup as $g){
            $videos = $this->multiGetPageList($g, $pageSize, $param);
            Functions::consoleDump('count:'. count($videos));

            // 保存
            foreach($videos as $video) {
                $this->save($video);
            }
        }

        $t = microtime(1) - $t;
        var_dump($t);

        return true;
    }

    /**
     * @param $video
     * @return bool
     * 保存
     */
    public function save($video){
        $ksVideoModel = new KsVideoModel();
        $ksVideo = $ksVideoModel->where('id', $video['photo_id'])->first();

        if(empty($ksVideo)){
            $ksVideo = new KsVideoModel();
        }

        $ksVideo->id = $video['photo_id'];
        $ksVideo->width = $video['width'];
        $ksVideo->height = $video['height'];
        $ksVideo->url = $video['url'];
        $ksVideo->cover_url = $video['cover_url'];
        $ksVideo->signature = $video['signature'];
        $ksVideo->upload_time = $video['upload_time'];
        $ksVideo->photo_name = $video['photo_name'];
        $ksVideo->duration = $video['duration'];
        $ksVideo->source = $video['source'];

        $ret = $ksVideo->save();

        if($ret){
            // 添加关联关系
            $this->relationAccount($video['advertiser_id'],$video['photo_id']);
        }

        return $ret;
    }


    /**
     * @param $accountId
     * @param $videoId
     * @return KsAccountVideoModel
     * 关联账户
     */
    public function relationAccount($accountId,$videoId){
        $ksAccountVideoModel = new KsAccountVideoModel();
        $ksAccountVideo = $ksAccountVideoModel->where('account_id', $accountId)
            ->where('video_id', $videoId)
            ->first();

        if(empty($ksAccountVideo)){
            $ksAccountVideo = new KsAccountVideoModel();
            $ksAccountVideo->account_id = $accountId;
            $ksAccountVideo->video_id = $videoId;
            $ksAccountVideo->save();
        }
        return $ksAccountVideo;
    }
}
