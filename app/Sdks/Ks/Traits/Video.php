<?php

namespace App\Sdks\Ks\Traits;

trait Video
{
    /**
     * @param $accountId
     * @param $signature
     * @param $file
     * @param array $param
     * @return mixed
     * 上传
     */
    public function uploadVideo($accountId, $signature, $file, $param = []){
        $url = $this->getUrl('/v2/file/ad/video/upload');

        $param = array_merge([
            'advertiser_id' => $accountId,
            'signature' => $signature,
            'file' => $file,
        ], $param);

        return $this->fileRequest($url, $param, 'POST');
    }

    /**
     * @param $accountId
     * @param array $photoIds
     * @param array $targetAccountIds
     * @return mixed
     * 推送
     */
    public function pushVideo($accountId, array $targetAccountIds, array $photoIds){
        $url = $this->getUrl('/v1/file/ad/video/share');

        $param = [
            'advertiser_id' => $accountId,
            'share_advertiser_ids' => $targetAccountIds,
            'photo_ids' => $photoIds,
        ];

        return $this->authRequest($url, $param, 'POST');
    }

    /**
     * @param array $accounts
     * @param int $page
     * @param int $pageSize
     * @param array $param
     * @return mixed
     * 并发获取视频列表
     */
    public function multiGetVideoList(array $accounts, $page = 1, $pageSize = 10, $param = []){
        $url = $this->getUrl('v1/file/ad/video/list');

        return $this->multiGetPageList($url, $accounts, $page, $pageSize, $param);
    }
}
