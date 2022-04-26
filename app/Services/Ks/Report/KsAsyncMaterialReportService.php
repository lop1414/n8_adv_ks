<?php

namespace App\Services\Ks\Report;


use App\Common\Enums\MaterialTypeEnums;
use App\Common\Tools\CustomException;
use App\Common\Tools\CustomRedis;
use App\Datas\KsMaterialData;
use App\Models\Ks\Report\KsMaterialReportModel;


class KsAsyncMaterialReportService extends KsReportService
{

    public function __construct($appId = ''){
        parent::__construct($appId);

        $this->modelClass = KsMaterialReportModel::class;
    }


    public function createTask($accountIds,$date): bool
    {
        $accountGroup = $this->getAccountGroup($accountIds);

        $params = [
            'start_date' => $date,
            'end_date'   => $date,
            'view_type'  => 5
        ];
        foreach ($accountGroup as $accounts) {
            foreach ($accounts as $account) {
                try{
                    $this->sdk->setAccessToken($account['access_token']);
                    $this->sdk->createAsyncTask($account['account_id'], $params);
                }catch(CustomException $e){
                    $errInfo = $e->getErrorInfo();
                    if(isset($errInfo->data->result->code) && $errInfo->data->result->code == 401000){
                        echo $errInfo->data->result->message."\n";
                    }else{
                        var_dump($errInfo);
                    }
                }
            }
        }
        return true;

    }


    public function syncTaskData($accountIds)
    {
        $accountGroup = $this->getAccountGroup($accountIds);

        $pageSize = 20;
        $downloadTasks = [];
        $customRedis = new CustomRedis();

        foreach ($accountGroup as $accounts) {
            foreach ($accounts as $account) {
                $page = 1;
                $this->sdk->setAccessToken($account['access_token']);
                do {
                    $data = $this->sdk->getAsyncTask($account['account_id'], $page, $pageSize);
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
        }


        $ksMaterialData = new KsMaterialData();
        foreach ($downloadTasks as $task){

            $csv = $this->sdk->downloadAsyncTaskCsv($task['advertiser_id'],$task['task_id']);
            $csvData = str_getcsv($csv,"\n");
            $fields = [];

            $data = [];
            foreach ($csvData as $key => $raw){
                $raw = str_getcsv($raw,',');
                if($key == 0){
                    foreach ($raw as $index => $field){
                        if($index == 0){
                            // photo_id 字段前有特殊字符
                            $fields[$index] = 'photo_id';
                            continue;
                        }
                        $fields[$index] = $field;
                    }
                    continue;
                }

                $item = [];

                foreach ($raw as $k => $v){
                    $item[$fields[$k]] = $v;
                }

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

                $data[] = $item;
            }

            $this->batchSave($data);

            $customRedis->set($task['task_name'],1);
            $customRedis->expire($task['task_name'],7200);
        }

    }


}
