<?php

namespace App\Services\Ks\Report;

use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;
use App\Services\KuaiShouService;
use Illuminate\Support\Facades\DB;

class KsReportService extends BaseService
{
    /**
     * @var string
     * 模型类
     */
    public $modelClass;

    public function getContainer(KuaiShou $ksSdk): ApiContainer
    {
        var_dump("实现该方法");
    }



    /**
     * api 请求参数
     * @return array
     */
    public function getApiReqParams(): array
    {
        return ['temporal_granularity' => 'HOURLY'];
    }


    public function sync(array $option = []): bool
    {
        try{
            ini_set('memory_limit', '2048M');

            $t = microtime(1);

            $accountIds = $option['account_ids'] ?? [];

            // 并发分片大小
            $multiChunkSize = min(intval($option['multi_chunk_size'] ?? 0 ),10);


            // 在跑账户
            /*if(!empty($option['running'])){
                $runningAccountIds = $this->getRunningAccountIds();
                if(!empty($accountIds)){
                    $accountIds = array_intersect($accountIds, $runningAccountIds);
                }else{
                    $accountIds = $runningAccountIds;
                }
            }*/

            list($startDate,$endDate) = Functions::getDateRange($option['date']);

            // 删除
            if(!empty($option['delete'])){

                 (new $this->modelClass())
                    ->whereBetween('stat_datetime', [$startDate .' 00:00:00', $endDate .' 23:59:59'])
                    ->when($accountIds,function ($builder,$accountIds){
                        return  $builder->whereIn('account_id', $accountIds);
                    })
                    ->delete();
            }

            if(!empty($option['run_by_account_charge'])){
                // 处理广告账户id
                $accountIds = $this->runByAccountCharge($accountIds);
            }

            // 获取子账户组
            $accountGroup = KuaiShouService::getAccountGroupByToken($accountIds);

            $pageSize = 200;
            $charge = 0;
            $param = array_merge([
                'start_date'       => $startDate,
                'end_date'         => $endDate
            ],$this->getApiReqParams());

            foreach($accountGroup as $token => $accountList){
                $ksSdk = KuaiShou::init($token);

                $accountChunk = array_chunk($accountList,5);
                foreach ($accountChunk as $accounts){
                    $saveData = [];
                    $accountIds = array_column($accounts,'account_id');
                    $data = KuaiShouService::multiGet($this->getContainer($ksSdk),$accountIds,$param,1,$pageSize);
                    foreach($data as $item) {
                        $charge += $item['charge'];

                        if(!$this->itemValid($item)){
                            continue;
                        }

                        $item['stat_datetime'] = "{$item['stat_date']} {$item['stat_hour']}:00:00";
                        $item['extends'] = json_encode($item);
                        $item['charge'] = bcmul($item['charge'],1000);
                        $saveData[] = $item;
                    }
                    $this->batchSave($saveData);
                }
            }

            Functions::consoleDump('charge:'. $charge);
            $t = microtime(1) - $t;
            Functions::consoleDump($t);

        }catch (\Exception $e){
            throw new CustomException([
                'code' => 'SYNC_REPORT_ERROR',
                'message' => '同步报表异常',
                'log' => true,
                'data' => [
                    'option' => $option
                ],
            ]);
        }

        return true;
    }

    /**
     * @param $item
     * @return bool
     * 校验
     */
    protected function itemValid($item): bool
    {
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
     * 按账户消耗执行
     * @param array $accountIds
     * @return string[]
     * @throws CustomException
     */
    protected function runByAccountCharge(array $accountIds):array
    {
        $accountReportMap = array_column((new KsAccountReportService())->getAccountReportByDate(),'charge','account_id');

        $creativeReportMap = array_column($this->getAccountReportByDate(),'charge','account_id');

        $creativeAccountIds = ['xx'];
        foreach($accountReportMap as $accountId => $charge){
            if(isset($creativeReportMap[$accountId]) && bcsub($creativeReportMap[$accountId] * 1000, $charge * 1000) >= 0){
                continue;
            }
            $creativeAccountIds[] = $accountId;
        }

        return $creativeAccountIds;
    }

    /**
     * @param $data
     * @return bool
     * 批量保存
     */
    public function batchSave($data): bool
    {
        $model = new $this->modelClass();
        $model->chunkInsertOrUpdate($data);
        return true;
    }


    /**
     * 按日期获取有消耗账户
     * @param string $date
     * @return array
     * @throws CustomException
     */
    public function getAccountReportByDate(string $date = 'today'): array
    {
        $date = Functions::getDate($date);
        Functions::dateCheck($date);

        $model = new $this->modelClass();
        $report = $model->whereBetween('stat_datetime', ["{$date} 00:00:00", "{$date} 23:59:59"])
            ->groupBy('account_id')
            ->orderBy('charge', 'DESC')
            ->select(DB::raw("account_id, SUM(charge) charge"))
            ->get();
        if($report->isEmpty()){
            return [];
        }

        return $report->toArray();
    }
}
