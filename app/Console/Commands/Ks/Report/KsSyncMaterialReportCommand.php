<?php

namespace App\Console\Commands\Ks\Report;

use App\Common\Console\BaseCommand;
use App\Services\Ks\Report\KsAsyncMaterialReportService;
use App\Services\Ks\Report\KsMaterialReportService;

class KsSyncMaterialReportCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'ks:sync_material_report  {--date=} {--account_ids=} {--delete=} {--running=} {--multi_chunk_size=} {--key_suffix=} {--run_by_account_charge=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '同步快手素材报表';

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
        $lockKey = 'ks_sync_material_report';
        if(!empty($param['running'])){
            $lockKey .= '_running';
        }

        // key 日期
        if(!empty($param['date'])){
            $lockKey .= '_'. $param['date'];
        }

        // key 后缀
        if(!empty($param['key_suffix'])){
            $lockKey .= '_'. trim($param['key_suffix']);
        }

        $this->lockRun(
            [$this, 'exec'],
            $lockKey,
            43200,
            ['log' => true],
            $param
        );
    }

    /**
     * @param $param
     * @return bool
     * @throws \App\Common\Tools\CustomException
     * 执行
     */
    protected function exec($param){
        // 时间超过7天前 则采用异步任务获取
        if($param['date'] <= date('Y-m-d',strtotime('-7 days'))){
            $this->asyncReport($param);
            return true;
        }

        $ksMaterialReportService = new KsMaterialReportService();
        $ksMaterialReportService->sync($param);

        return true;
    }


    protected function asyncReport($param){
        echo "asyncReport\n";
        $ksMaterialReportService = new KsAsyncMaterialReportService();
        $ksMaterialReportService->createTask($param['account_ids'],$param['date']);
        $ksMaterialReportService->syncTaskData($param['account_ids']);
    }
}
