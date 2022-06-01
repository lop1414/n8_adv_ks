<?php

namespace App\Services\Ks\Report;


use App\Common\Enums\MaterialTypeEnums;
use App\Common\Tools\CustomException;
use App\Common\Tools\CustomRedis;
use App\Datas\KsMaterialData;
use App\Models\Ks\Report\KsMaterialReportModel;
use App\Sdks\KuaiShou\Kernel\ApiContainer;
use App\Sdks\KuaiShou\KuaiShou;
use App\Services\KuaiShouService;


class KsAsyncMaterialReportService extends KsReportService
{
    public function setModelClass(): bool
    {
        $this->modelClass = KsMaterialReportModel::class;
        return true;
    }


    public function getContainer(KuaiShou $ksSdk): ApiContainer
    {
        return $ksSdk->asyncTack();
    }


    public function createTask($accountIds,$date): bool
    {
        $accountGroup = KuaiShouService::getAccountGroupByToken($accountIds);

        $params = [
            'start_date' => $date,
            'end_date'   => $date,
            'view_type'  => 5
        ];

        foreach ($accountGroup as $token => $accounts) {
            $ksSdk = KuaiShou::init($token);
            foreach ($accounts as $account) {
                try{
                    $ksSdk->asyncTack()->create([
                        'advertiser_id' => $account['account_id'],
                        'task_name'     => "material_report:{$account['account_id']}:{$params['start_date']}",
                        'task_params' => $params
                    ]);
                }catch(\Exception $e){

                    if($e->getCode() == 401000){
                        echo $e->getMessage()."\n";
                    }else{
                        var_dump($e);
                    }
                }
            }
        }
        return true;

    }


    public function syncTaskData($accountIds)
    {
        $this->setModelClass();
        $accountGroup = KuaiShouService::getAccountGroupByToken($accountIds);

        $pageSize = 20;
        $downloadTasks = [];
        $customRedis = new CustomRedis();
        $ksMaterialData = new KsMaterialData();

        foreach ($accountGroup as $token =>  $accounts) {
            $ksSdk = KuaiShou::init($token);

            foreach ($accounts as $account) {
                $page = 1;

                do {
                    $data = $ksSdk->asyncTack()->get([
                        'advertiser_id' => $account['account_id'],
                        'page'  => $page,
                        'page_size' => $pageSize
                    ]);

                    foreach ($data['details'] as $item) {
                        if($item['task_status'] != 2) continue;
                        $cacheInfo = $customRedis->get($item['task_name']);
                        if (!!$cacheInfo) {
                            continue;
                        }
                        $downloadTasks[] = $item;
                    }

                    $totalPage = ceil($data['total_count'] / $pageSize);
                    $page += 1;
                } while ($page <= $totalPage);
            }


            foreach ($downloadTasks as $task){
                $data = $ksSdk->asyncTack()->getDownloadData([
                    'advertiser_id' => $task['advertiser_id'],
                    'task_id'       => $task['task_id']
                ]);

                $saveData = [];
                foreach ($data as $item){

                    if(!$this->itemValid($item)){
                        continue;
                    }

                    $item['extends'] = json_encode($item);

                    $item['account_id'] = $task['advertiser_id'];
                    if($item['stat_hour'] == 0) $item['stat_hour'] = '00';

                    $item['stat_datetime'] = "{$item['stat_date']} {$item['stat_hour']}:00:00";
                    $item['charge'] = bcmul($item['charge'],1000);

                    $ksMaterial = $ksMaterialData->save([
                        'material_type' => MaterialTypeEnums::VIDEO,
                        'file_id'       => $item['photo_id']
                    ]);
                    $item['material_id'] = $ksMaterial['id'];

                    $saveData[] = $item;
                }

                $this->batchSave($saveData);

                $customRedis->set($task['task_name'],1);
                $customRedis->expire($task['task_name'],7200);
            }
        }
    }


}
