<?php

namespace App\Services;

use App\Common\Enums\StatusEnum;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Models\Ks\KsAccountModel;
use App\Models\Ks\KsUserModel;
use App\Sdks\KuaiShou\Kernel\ApiContainer;


class KuaiShouService extends BaseService
{


    /**
     * @param $userId
     * @return mixed
     * @throws CustomException
     * 获取快手用户
     */
    static public function getKsUser($userId):array
    {
        $ksUserModel = new KsUserModel();
        $user = $ksUserModel
            ->where('id', $userId)
            ->first();

        if(empty($user)){
            throw new CustomException([
                'code' => 'NOT_FOUND_USER',
                'message' => '找不到对应用户,请确认用户是否正确',
                'data' => [
                    'user_id' => $userId,
                ],
            ]);
        }
        return $user->toArray();
    }



    /**
     * @param array $accountIds
     * @return array
     * 获取账号组
     */
    static public function getAccountGroupByToken(array $accountIds = []): array
    {

        $builder = (new KsAccountModel())->where('status', StatusEnum::ENABLE);

        if(!empty($accountIds)){
            $accountIdsStr = implode("','", $accountIds);
            $builder->whereRaw("account_id IN ('{$accountIdsStr}')");
        }

        $accounts = $builder->get()->toArray();
        $accountGroup = [];
        foreach ($accounts as $account){
            $accountGroup[$account['access_token']][] = $account;
        }
        return $accountGroup;
    }



    /**
     * 并发获取分页列表
     * @param ApiContainer $container
     * @param array $accountIds
     * @param array $param
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    static public function multiGet(ApiContainer $container, array $accountIds, array $param = [],int $page = 1 ,int $pageSize = 100): array
    {
        $tmpContainer = $container;
        $param['page'] = $page;
        $param['page_size'] = $pageSize;

        $res = $container->multipleGet($accountIds,$param);

        $nextPageAccountIds = [];
        $list = [];
        foreach($res as $v){

            if(isset($v['code']) && $v['code'] != 0){
                var_dump("请求失败:".$v['message'],$v['request_params']);
                continue;

            }

            if(isset($v['total_count'])){
                // 兼容素材报表坑爹的情况
                $v['data'] = $v;
            }

            if(empty($v['data']['total_count'])){
                continue;
            }

            $advertiserId = $v['request_params']['advertiser_id'];


            foreach($v['data']['details'] as $item){
                $item['advertiser_id'] = $advertiserId;
                $item['account_id'] = $advertiserId;
                $list[] = $item;
            }

            $totalPage = ceil($v['data']['total_count'] / $pageSize);
            if($totalPage > $page){
                $nextPageAccountIds[] = $advertiserId;
            }
        }

        if(empty($nextPageAccountIds)){
            return  $list;
        }

        // 多页数据
        $page += 1;
        $tmp = self::multiGet($tmpContainer,$nextPageAccountIds,$param,$page,$pageSize);
        return array_merge($tmp, $list);
    }
}
