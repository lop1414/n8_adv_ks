<?php

namespace App\Services\Ks;

use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Models\Ks\KsCreativeModel;
use App\Models\Ks\KsProgramCreativeModel;

class KsProgramCreativeService extends KsService
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
        return $this->sdk->multiGetProgramCreativeList($accounts, $page, $pageSize, $param);
    }

    /**
     * @param array $option
     * @return bool
     * @throws CustomException
     * 同步
     */
    public function sync($option = []){
        $accountIds = [];
        // 账户id过滤
        if(!empty($option['account_ids'])){
            $accountIds = $option['account_ids'];
        }

        $param = [];
        if(!empty($option['date'])){
            $param['start_date'] = Functions::getDate($option['date']);
            $param['end_date'] = Functions::getDate($option['date']);
        }

        $accountGroup = $this->getAccountGroup($accountIds);

        $t = microtime(1);

        $pageSize = 100;
        foreach($accountGroup as $g){
            $creatives = $this->multiGetPageList($g, $pageSize, $param);
            Functions::consoleDump('count:'. count($creatives));


            // 保存
            foreach($creatives as $creative) {
                $this->save($creative);
            }
        }

        $t = microtime(1) - $t;
        var_dump($t);

        return true;
    }


    /**
     * @param $creative
     * @return bool
     * 保存
     */
    public function save($creative){
        $ksProgramCreativeModel = new KsProgramCreativeModel();
        $ksProgramCreative = $ksProgramCreativeModel->where('id', $creative['unit_id'])->first();

        if(empty($ksProgramCreative)){
            $ksProgramCreative = new KsProgramCreativeModel();
            $isChangeTrackUrl = true;
        }else{
            $isChangeTrackUrl = $ksProgramCreative->extends->click_url == $creative['click_url'] ? false :true;
        }

        $ksProgramCreative->id = $creative['unit_id'];
        $ksProgramCreative->account_id = $creative['account_id'];
        $ksProgramCreative->name = $creative['package_name'];
        $ksProgramCreative->put_status = $creative['put_status'];
        $ksProgramCreative->view_status = $creative['view_status'];
        $ksProgramCreative->create_time = $creative['create_time'];
        $ksProgramCreative->update_time = $creative['update_time'];
        $ksProgramCreative->extends = $creative;
        $ret = $ksProgramCreative->save();

        if($ret){
            // 添加关联关系
            $ksVideoService = new KsVideoService();

            if(!empty($creative['horizontal_photo_ids'])){
                foreach ($creative['horizontal_photo_ids'] as $photoId){
                    $ksVideoService->relationAccount($ksProgramCreative['account_id'],$photoId);
                }
            }
            if(!empty($creative['vertical_photo_ids'])){
                foreach ($creative['vertical_photo_ids'] as $photoId){
                    $ksVideoService->relationAccount($ksProgramCreative['account_id'],$photoId);
                }
            }

            if($isChangeTrackUrl){
                // 更改计划渠道关联
            }
        }

        return $ret;
    }
}
