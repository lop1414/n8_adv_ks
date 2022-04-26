<?php

namespace App\Sdks\Ks\Traits;

trait AsyncTask
{
    /**
     * @param $accountId
     * @param $params
     * @return mixed
     * 创建任务
     */
    public function createAsyncTask($accountId,$params){
        $url = $this->getUrl('/v1/async_task/create');

        return $this->authRequest($url, [
            'advertiser_id' => $accountId,
            'task_name'     => "material|".$params['start_date'],
            'task_params'   => $params
        ], 'POST');
    }


    /**
     * @param $accountId
     * @param int $page
     * @param int $pageSize
     * @return mixed
     * 获取任务列表
     */
    public function getAsyncTask($accountId, int $page = 1, int $pageSize = 20){
        $url = $this->getUrl('/v1/async_task/list');

        return $this->authRequest($url, [
            'advertiser_id' => $accountId,
            'page'  => $page,
            'page_size' => $pageSize
        ], 'GET');
    }


    /**
     * @param $accountId
     * @param $taskId
     * @return mixed
     * 下载异步任务数据csv文件
     */
    public function downloadAsyncTaskCsv($accountId,$taskId){
        $url = $this->getUrl('/v1/async_task/download?advertiser_id='.$accountId.'&task_id='.$taskId);

        return $this->fileDownload($url, [
            'advertiser_id' => $accountId,
            'task_id'  => $taskId
        ], 'GET');
    }
}
