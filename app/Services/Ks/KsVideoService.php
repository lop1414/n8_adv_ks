<?php

namespace App\Services\Ks;

use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Models\Ks\KsAccountVideoModel;
use App\Models\Ks\KsVideoModel;
use App\Sdks\KuaiShou\Config;
use App\Sdks\KuaiShou\KuaiShou;
use App\Services\KuaiShouService;
use Exception;
use SplFileInfo;


class KsVideoService extends BaseService
{


    /**
     * 上传视频
     * @param string $token
     * @param int $accountId
     * @param string $signature
     * @param SplFileInfo $file
     * @param array $param
     * @return array|mixed
     * @throws Exception
     */
    public function uploadVideo(string $token, int $accountId, string $signature, SplFileInfo $file, array $param = []){
        Config::$timeout = 120;
        $ksSdk = KuaiShou::init($token);

        $ret = $ksSdk->video()->upload(array_merge([
            'advertiser_id' => $accountId,
            'file'          => $file,
            'signature'     => $signature,
            'upload_type'   => 1
        ],$param));
        Functions::consoleDump($ret);

        return $ret;
    }


    /**
     * 推送视频
     * @param string $token
     * @param int $accountId
     * @param array $targetAccountIds
     * @param array $photoIds
     * @return array
     * @throws Exception
     */
    public function pushVideo(string $token,int $accountId, array $targetAccountIds, array $photoIds): array
    {
        $ksSdk = KuaiShou::init($token);

        return $ksSdk->video()->share([
            'advertiser_id'         => $accountId,
            'photo_ids'             => $photoIds,
            'share_advertiser_ids'  => $targetAccountIds,
        ]);
    }


    public function sync(array $option = []): bool
    {
        ini_set('memory_limit', '2048M');

        $param = [];
        if(!empty($option['date'])){
            $param['start_time'] = Functions::getDate($option['date']);
            $param['end_time'] = Functions::getDate($option['date']);
        }

        if(!empty($option['ids'])){
            $param['photo_ids'] = $option['ids'];
        }

        $accountGroup = KuaiShouService::getAccountGroupByToken($option['account_ids'] ?? []);

        $t = microtime(1);

        foreach($accountGroup as $token => $accountList){

            $ksSdk = KuaiShou::init($token);
            $accountChunk = array_chunk($accountList,5);
            foreach ($accountChunk as $accounts){
                $accountIds = array_column($accounts,'account_id');
                $videos = KuaiShouService::multiGet($ksSdk->video(),$accountIds,$param);
                foreach($videos as $video) {
                    $this->save($video);
                }
            }
        }

        $t = microtime(1) - $t;
        var_dump($t);

        return true;
    }


    public function save(array $video): bool
    {
        $ksVideoModel = new KsVideoModel();
        $ksVideo = $ksVideoModel->where('id', $video['photo_id'])->first();

        if(empty($ksVideo)){
            $ksVideo = new KsVideoModel();
        }

        $ksVideo->id = $video['photo_id'];
        $ksVideo->width = $video['width'];
        $ksVideo->height = $video['height'];
        $ksVideo->url = $video['url'] ?? '';
        $ksVideo->cover_url = $video['cover_url'];
        $ksVideo->signature = $video['signature'];
        $ksVideo->upload_time = $video['upload_time'];
        $ksVideo->photo_name = $video['photo_name'];
        $ksVideo->duration = $video['duration'];
        $ksVideo->source = $video['source'];

        $ret = $ksVideo->save();

        if($ret){
            // 添加关联关系
            $this->relationAccount($video['advertiser_id'],$video['photo_id']);
        }

        return $ret;
    }


    /**
     * @param $accountId
     * @param $videoId
     * @return KsAccountVideoModel
     * 关联账户
     */
    public function relationAccount($accountId,$videoId){
        $ksAccountVideoModel = new KsAccountVideoModel();
        $ksAccountVideo = $ksAccountVideoModel->where('account_id', $accountId)
            ->where('video_id', $videoId)
            ->first();

        if(empty($ksAccountVideo)){
            $ksAccountVideo = new KsAccountVideoModel();
            $ksAccountVideo->account_id = $accountId;
            $ksAccountVideo->video_id = $videoId;
            $ksAccountVideo->save();
        }
        return $ksAccountVideo;
    }
}
