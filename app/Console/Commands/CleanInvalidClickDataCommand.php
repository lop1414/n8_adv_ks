<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Services\AdvClickService;

class CleanInvalidClickDataCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'clean_invalid_click_data  {--date=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '清除无效点击数据';

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

        $lockKey = 'clean_invalid_click_data';

        $option = ['log' => true];
        $this->lockRun(
            [$this, 'exec'],
            $lockKey,
            43200,
            $option,
            $param
        );
    }

    /**
     * @param $param
     * @return bool
     * @throws CustomException
     * 执行
     */
    public function exec($param){
        // 获取日期范围
        $dateRange = Functions::getDateRange($param['date']);
        $dateList = Functions::getDateListByRange($dateRange);

        $advClickService = new AdvClickService();
        foreach($dateList as $date){
            $dateTime = date('Y-m-d H:i:s',strtotime($date) - 60*60*24*7);
            $data = $advClickService->cleanInvalidClick($dateTime);
            echo $dateTime.' 已删除：'.$data['del_count'];
        }

        return true;
    }

}
