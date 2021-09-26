<?php

namespace App\Services\Ks\Report;

use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Services\Ks\KsService;
use Illuminate\Support\Facades\DB;

class KsReportService extends KsService
{
    /**
     * @var string
     * 模型类
     */
    public $modelClass;

    /**
     * OceanAccountReportService constructor.
     * @param string $appId
     */
    public function __construct($appId = ''){
        parent::__construct($appId);
    }

    /**
     * @param array $option
     * @return bool
     * @throws CustomException
     * 同步
     */
    public function sync($option = []){
        ini_set('memory_limit', '2048M');

        $t = microtime(1);

        $accountIds = [];
        // 账户id过滤
        if(!empty($option['account_ids'])){
            $accountIds = $option['account_ids'];
        }

        // 并发分片大小
        if(!empty($option['multi_chunk_size'])){
            $multiChunkSize = min(intval($option['multi_chunk_size']), 8);
            $this->sdk->setMultiChunkSize($multiChunkSize);
        }

        // 在跑账户
//        if(!empty($option['running'])){
//            $runningAccountIds = $this->getRunningAccountIds();
//            if(!empty($accountIds)){
//                $accountIds = array_intersect($accountIds, $runningAccountIds);
//            }else{
//                $accountIds = $runningAccountIds;
//            }
//        }

        $dateRange = Functions::getDateRange($option['date']);
        $dateList = Functions::getDateListByRange($dateRange);

        // 删除
        if(!empty($option['delete'])){
            $between = [
                $dateRange[0] .' 00:00:00',
                $dateRange[1] .' 23:59:59',
            ];

            $model = new $this->modelClass();

            $builder = $model->whereBetween('stat_datetime', $between);

            if(!empty($accountIds)){
                $builder->whereIn('account_id', $accountIds);
            }

            $builder->delete();
        }

        if(!empty($option['run_by_account_charge'])){
            // 处理广告账户id
            $accountIds = $this->runByAccountCharge($accountIds);
        }

        // 获取子账户组
        $accountGroup = $this->getAccountGroup($accountIds);

        foreach($dateList as $date){
            $param = [
                'start_date_min' => $date. ' 00:00:00',
                'end_date_min' => $date. ' 23:59:59',
                'temporal_granularity' => 'HOURLY',
            ];

            $pageSize = 200;
            foreach($accountGroup as $g){
                $items = $this->multiGetPageList($g, $pageSize, $param);

                Functions::consoleDump('count:'. count($items));

                $charge = 0;

                // 保存
                $data = [];
                foreach($items as $item) {
                    $charge += $item['charge'];

                    if(!$this->itemValid($item)){
                        continue;
                    }

                    $item['stat_datetime'] = "{$item['stat_date']} {$item['stat_hour']}:00:00";

                    $item['extends'] = json_encode($item);
                    $item['charge'] = bcmul($item['charge'],1000);
                    $data[] = $item;
                }

                // 批量保存
                $this->batchSave($data);

                Functions::consoleDump('charge:'. $charge);
            }
        }

        $t = microtime(1) - $t;
        Functions::consoleDump($t);

        return true;
    }

    /**
     * @param $item
     * @return bool
     * 校验
     */
    protected function itemValid($item){
        $valid = true;

        if(
            empty($item['charge']) &&
            empty($item['show']) &&
            empty($item['photo_click']) &&
            empty($item['aclick']) &&
            empty($item['bclick'])
        ){
            $valid = false;
        }

        return $valid;
    }

    /**
     * @param $accountIds
     * @return mixed
     * 按账户消耗执行
     */
    protected function runByAccountCharge($accountIds){
        return $accountIds;
    }

    /**
     * @param $data
     * @return bool
     * 批量保存
     */
    public function batchSave($data){
        $model = new $this->modelClass();
        $model->chunkInsertOrUpdate($data);
        return true;
    }

    /**
     * @param string $date
     * @return mixed
     * @throws CustomException
     * 按日期获取账户报表
     */
    public function getAccountReportByDate($date = 'today'){
        $date = Functions::getDate($date);
        Functions::dateCheck($date);

        $model = new $this->modelClass();
        $report = $model->whereBetween('stat_datetime', ["{$date} 00:00:00", "{$date} 23:59:59"])
            ->groupBy('account_id')
            ->orderBy('charge', 'DESC')
            ->select(DB::raw("account_id, SUM(charge) charge"))
            ->get();

        return $report;
    }
}
