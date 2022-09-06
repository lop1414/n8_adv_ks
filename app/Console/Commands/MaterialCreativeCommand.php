<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Services\Ks\Report\KsProgramCreativeReportService;

class MaterialCreativeCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'material_creative  {--date=} {--key_suffix=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '分析素材创意';

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

        // 锁 key
        $lockKey = 'material_creative';
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

        $ksProgramCreativeReportService = new KsProgramCreativeReportService();

        $dateRange = Functions::getDateRange($param['date']);
        $dateList = Functions::getDateListByRange($dateRange);
        foreach ($dateList as $date){
            $ksProgramCreativeReportService->creativeMaterial($date);
        }

        return true;
    }
}
