<?php

namespace App\Console\Commands\Ks;

use App\Common\Console\BaseCommand;
use App\Services\Ks\ksAccountService;

class KsRefreshAccessTokenCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'ks:refresh_access_token {--key_suffix=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '快手刷新access_token';

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
        $lockKey = 'ks_refresh_access_token';

        // key 后缀
        if(!empty($param['key_suffix'])){
            $lockKey .= '_'. trim($param['key_suffix']);
        }

        $ksAccountService = new KsAccountService();
        $option = ['log' => true];
        $this->lockRun(
            [$ksAccountService, 'refreshAccessToken'],
            $lockKey,
            43200,
            $option,
            $param
        );
    }
}
