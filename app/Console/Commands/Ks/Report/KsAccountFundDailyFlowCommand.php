<?php

namespace App\Console\Commands\Ks\Report;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Services\Ks\Report\KsAccountFundDailyFlowService;

class KsAccountFundDailyFlowCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'ks:sync_account_fund_daily_flow  {--date=} {--account_ids=} {--delete=} {--has_history_cost=} {--running=} {--key_suffix=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '同步快手广告账户流水信息';

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
        $lockKey = 'sync_account_fund_daily_flow';
        if(!empty($param['running'])){
            $lockKey .= '_running';
        }

        // key 日期
        if(!empty($param['date'])){
            $lockKey .= '_'. Functions::getDate($param['date']);
        }

        // key 后缀
        if(!empty($param['key_suffix'])){
            $lockKey .= '_'. trim($param['key_suffix']);
        }

        $ksAccountFundDailyFlowService = new KsAccountFundDailyFlowService();
        $option = ['log' => true];
        $this->lockRun(
            [$ksAccountFundDailyFlowService, 'sync'],
            $lockKey,
            43200,
            $option,
            $param
        );
    }
}
