<?php

namespace App\Services\Ks;

use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Models\Ks\KsCreativeModel;

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
        return $this->sdk->multiGetCreativeList($accounts, $page, $pageSize, $param);
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
        $ksCreativeModel = new KsCreativeModel();
        $ksCreative = $ksCreativeModel->where('id', $creative['creative_id'])->first();

        if(empty($ksCreative)){
            $ksCreative = new KsCreativeModel();
        }

        $ksCreative->id = $creative['creative_id'];
        $ksCreative->account_id = $creative['account_id'];
        $ksCreative->unit_id = $creative['unit_id'];
        $ksCreative->name = $creative['creative_name'];
        $ksCreative->creative_material_type = $creative['creative_material_type'];
        $ksCreative->photo_id = $creative['photo_id'];
        $ksCreative->status = $creative['status'];
        $ksCreative->put_status = $creative['put_status'];
        $ksCreative->create_channel = $creative['create_channel'];
        $ksCreative->create_time = $creative['create_time'];
        $ksCreative->update_time = $creative['update_time'];
        $ksCreative->extends = $creative;
        $ret = $ksCreative->save();

        if($ret){
            // 添加关联关系
            (new KsVideoService())->relationAccount($ksCreative['account_id'],$ksCreative['photo_id']);
        }

        return $ret;
    }
}
