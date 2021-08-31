<?php

namespace App\Sdks\Ks;

use App\Sdks\Ks\Traits\AccessToken;
use App\Sdks\Ks\Traits\Account;
use App\Sdks\Ks\Traits\App;
use App\Sdks\Ks\Traits\Campaign;
use App\Sdks\Ks\Traits\Creative;
use App\Sdks\Ks\Traits\Error;
use App\Sdks\Ks\Traits\Multi;
use App\Sdks\Ks\Traits\Report;
use App\Sdks\Ks\Traits\Request;
use App\Sdks\Ks\Traits\Video;

class Ks
{
    use App;
    use Account;
    use AccessToken;
    use Request;
    use Video;
    use Multi;
    use Error;
    use Report;
    use Creative;
    use Campaign;

    /**
     * 公共接口地址
     */
    const BASE_URL = 'https://ad.e.kuaishou.com/rest/openapi';

    /**
     * @param $uri
     * @return string
     * 获取请求地址
     */
    public function getUrl($uri){
        return self::BASE_URL .'/'. ltrim($uri, '/');
    }
}
