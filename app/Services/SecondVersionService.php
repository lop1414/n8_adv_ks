<?php

namespace App\Services;

use App\Common\Enums\AdvAccountBelongTypeEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Services\BaseService;
use App\Common\Services\ErrorLogService;
use App\Common\Services\SystemApi\CenterApiService;
use App\Common\Tools\CustomException;
use App\Models\Ks\KsAccountModel;
use App\Services\Ks\KsService;

class SecondVersionService extends BaseService
{
    /**
     * @var mixed
     * 接口域名
     */
    public $baseUrl;

    /**
     * @var mixed
     * 接口密钥
     */
    public $secret;

    /**
     * SecondVersionService constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->baseUrl = config('common.second_version_api.url');
        $this->secret = config('common.second_version_api.key');
    }

    /**
     * @param int $page
     * @param int $pageSize
     * @return mixed
     * @throws CustomException
     * 获取多个广告账户
     */
    public function getKsAdvAccounts($page = 1, $pageSize = 10){
        $url = $this->baseUrl .'/api/adv_account/kuai_shou/get';

        $param = [
            'page' => $page,
            'page_size' => $pageSize,
        ];

        return $this->publicRequest($url, $param);
    }

    /**
     * @param $appId
     * @param $accountId
     * @return mixed
     * @throws CustomException
     * 获取单个账户
     */
    public function getKsAdvAccount($appId, $accountId){
        $url = $this->baseUrl .'/api/adv_account/kuai_shou/get';

        $param = [
            'page' => 1,
            'page_size' => 1,
            'app_id' => $appId,
            'account_id' => $accountId,
        ];

        $data = $this->publicRequest($url, $param);

        return current($data['list']);
    }

    /**
     * @param int $pageSize
     * @return array
     * 获取所有广告账户
     */
    public function getKsAllAdvAccount($pageSize = 100){
        // 获取所有
        $all = $this->getPageListAll(function($page) use($pageSize){
            return $this->getKsAdvAccounts($page, $pageSize);
        });

        return $all;
    }

    /**
     * @param $func
     * @param int $page
     * @param int $pageSize
     * @return array
     * 获取分页列表所有数据
     */
    public function getPageListAll($func, $page = 1, $pageSize = 100){
        $all = [];
        do{
            $data = $func($page);

            $all = array_merge($all, $data['list']);

            $totalPage = $data['page_info']['total_page'] ?? 0;

            $page++;

            sleep(1);
        }while($page <= $totalPage);

        return $all;
    }

    /**
     * @param $url
     * @param array $param
     * @param string $method
     * @param array $header
     * @return mixed
     * @throws CustomException
     * 公共请求
     */
    public function publicRequest($url, $param = [], $method = 'POST', $header = []){
        // 构造签名
        $param['time'] = $param['time'] ?? time();
        $param['sign'] = $this->buildSign($param);

        $param = json_encode($param);

        $header = array_merge([
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($param)
        ], $header);

        $ret = $this->curlRequest($url, $param, $method, $header);

        $result = json_decode($ret, true);

        if(empty($result) || $result['code'] != 0){
            // 错误提示
            $errorMessage = '二版接口请求错误';

            throw new CustomException([
                'code' => 'SECOND_VERSION_API_REQUEST_ERROR',
                'message' => $errorMessage,
                'log' => true,
                'data' => [
                    'url' => $url,
                    'header' => $header,
                    'param' => $param,
                    'result' => $result,
                ],
            ]);
        }

        return $result['data'];
    }

    /**
     * @param $param
     * @return string
     * 构建签名
     */
    public function buildSign($param){
        return md5($this->secret . $param['time']);
    }

    /**
     * @param $url
     * @param $param
     * @param string $method
     * @param array $header
     * @return bool|string
     * CURL请求
     */
    private function curlRequest($url, $param = [], $method = 'POST', $header = []){
        $method = strtoupper($method);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $header = array_merge($header, ['Connection: close']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        if(stripos($url, 'https://') === 0){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_POST, true);
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * @param array $option
     * @return bool
     * @throws CustomException
     * 同步快手帐号
     */
    public function syncKsAccount($option = []){
        $isSyncAccountInfo = $option['is_sync_account_info'] ?? false;

        $secondVersionAccounts = $this->getKsAllAdvAccount();

        // 管理员映射
        $centerApiService = new CenterApiService();
        $adminUsers = $centerApiService->apiGetAdminUsers();
        $adminUserMap = array_column($adminUsers, 'id', 'name');

        foreach($secondVersionAccounts as $account){
            $ksAccountModel = new KsAccountModel();
            $ksAccount = $ksAccountModel->where('belong_platform', AdvAccountBelongTypeEnum::SECOND_VERSION)
                ->where('app_id', $account['app_id'])
                ->where('account_id', $account['adv_id'])
                ->first();

            if(empty($ksAccount)){
                // 新增
                $ksAccount = new KsAccountModel();
                $ksAccount->app_id = $account['app_id'];
                $ksAccount->name = $account['name'];
                $ksAccount->belong_platform = AdvAccountBelongTypeEnum::SECOND_VERSION;
                $ksAccount->account_id = $account['adv_id'];
                $ksAccount->access_token = $account['token'];
                $ksAccount->refresh_token = '';
                $ksAccount->fail_at = $account['fail_at'];
                $ksAccount->extend = [];
                $ksAccount->parent_id = $account['parent_adv_id'];
                $ksAccount->status = StatusEnum::ENABLE;
                $ksAccount->admin_id = $adminUserMap[$account['admin_name']] ?? 0;
            }else{
                // 更新
                $ksAccount->access_token = $account['token'];
                $ksAccount->fail_at = $account['fail_at'];
                $ksAccount->parent_id = $account['parent_adv_id'];
                $ksAccount->admin_id = $adminUserMap[$account['admin_name']] ?? 0;
            }

            // 保存
            $ksAccount->save();
        }

        $isSyncAccountInfo && $this->syncKsAccountInfo();

        return true;
    }

    /**
     * @return bool
     * 同步账户信息
     */
    public function syncKsAccountInfo(){
        // 获取公司为空的账户
        $ksAccountModel = new KsAccountModel();
        $ksAccounts = $ksAccountModel->where('belong_platform', AdvAccountBelongTypeEnum::SECOND_VERSION)
            ->where('company', '')
            ->where('status', StatusEnum::ENABLE)
            ->get();

        foreach($ksAccounts as $ksAccount){
            try{
                // 获取账户信息
                $ksService = new KsService($ksAccount->app_id);
                $ksService->setAccountId($ksAccount->account_id);
                $accountInfo = $ksService->getAccountInfo($ksAccount->account_id);

                // 保存
                $ksAccount->company = $accountInfo['corporation_name'] ?? '';
                $ksAccount->user_id = $accountInfo['user_id'] ?? '';
                $ksAccount->save();
            }catch(CustomException $e){
                $errorLogService = new ErrorLogService();
                $errorLogService->catch($e);
            }catch(\Exception $e){
                $errorLogService = new ErrorLogService();
                $errorLogService->catch($e);
            }
        }

        return true;
    }
}
