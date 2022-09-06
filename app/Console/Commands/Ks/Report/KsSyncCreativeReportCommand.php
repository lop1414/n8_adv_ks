<?php

namespace App\Console\Commands\Ks\Report;

use App\Common\Console\BaseCommand;
use App\Services\Ks\Report\KsCreativeReportService;

class KsSyncCreativeReportCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'ks:sync_creative_report  {--date=} {--account_ids=} {--delete=} {--running=} {--multi_chunk_size=} {--key_suffix=} {--run_by_account_charge=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '同步快手自定义创意报表';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * @throws \App\Common\Tools\CustomException
     * 处理
     */
    public function handle(){
        $param = $this->option();

        // 账户
        if(!empty($param['account_ids'])){
            $param['account_ids'] = explode(",", $param['account_ids']);
        }

        // 锁 key
        $lockKey = 'ks_sync_creative_report';
        $lockKey .= isset($param['running']) ? '_running' : '';
        $lockKey .= isset($param['date']) ? '_'.$param['date'] : '';  // key 日期
        $lockKey .= isset($param['key_suffix']) ? '_'.$param['key_suffix'] : '';  // key 后缀


        $this->lockRun([$this, 'exec'], $lockKey, 43200, ['log' => true], $param);
    }

    /**
     * @param $param
     * @return bool
     * @throws \App\Common\Tools\CustomException
     * 执行
     */
    protected function exec($param){
        $ksCreativeReportService = new KsCreativeReportService();
        $ksCreativeReportService->sync($param);

        return true;
    }
}
