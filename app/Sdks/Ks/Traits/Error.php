<?php

namespace App\Sdks\Ks\Traits;

trait Error
{
    /**
     * @return array
     * 获取返回映射
     */
    public function getCodeMessageMap(){
        return [
            // 成功返回
            0 => '成功',

            // 限流错误
            400001 => '请求过于频繁',
        ];
    }

    /**
     * @param $result
     * @return bool
     * 是否网络错误
     */
    public function isNetworkError($result){
        if(empty($result)){
            return true;
        }

        return false;
    }
}
