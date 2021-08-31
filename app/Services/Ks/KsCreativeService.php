<?php

namespace App\Services\Ks;

use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Enums\Ks\KsSyncTypeEnum;
use App\Models\Ks\KsAccountVideoModel;
use App\Models\Ks\KsVideoModel;
use App\Services\Task\TaskKsSyncService;

class KsCreativeService extends KsService
{
    /**
     * constructor.
     * @param string $appId
     */
    public function __construct($appId = ''){
        parent::__construct($appId);
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
        return $this->sdk->multiGetPackageCreativeList($accounts, $page, $pageSize, $param);
    }

    /**
     * @param array $option
     * @return bool
     * @throws CustomException
     * 同步
     */
    public function sync($option = []){
        $option = [
            'account_ids' => '10157725',
            'date' => '2021-08-30'
        ];
        $accountIds = [];
        // 账户id过滤
        if(!empty($option['account_ids'])){
            $accountIds = $option['account_ids'];
        }

        $param = [];
        if(!empty($option['date'])){
            $param['start'] = Functions::getDate($option['date']);
            $param['end'] = Functions::getDate($option['date']);
        }
        $param['unit_ids'] = [368827437];

        $accountGroup = $this->getAccountGroup($accountIds);

        $t = microtime(1);

        $pageSize = 100;
        foreach($accountGroup as $g){
            $creatives = $this->multiGetPageList($g, $pageSize, $param);
            dd($creatives);
            Functions::consoleDump('count:'. count($creatives));


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
            $ksAccountVideoModel = new KsAccountVideoModel();
            $ksAccountVideo = $ksAccountVideoModel->where('account_id', $video['advertiser_id'])
                ->where('video_id', $video['photo_id'])
                ->first();

            if(empty($ksAccountVideo)){
                $ksAccountVideo = new KsAccountVideoModel();
                $ksAccountVideo->account_id = $video['advertiser_id'];
                $ksAccountVideo->video_id = $video['photo_id'];
                $ksAccountVideo->save();
            }
        }

        return $ret;
    }
}
