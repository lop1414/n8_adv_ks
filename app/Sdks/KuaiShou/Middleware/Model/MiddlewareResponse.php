<?php

namespace App\Sdks\YueWen\Middleware\Model;

use App\Sdks\KuaiShou\Kernel\BaseObject;

class MiddlewareResponse extends BaseObject
{
    /**
     * @var mixed
     */
    public $response;

    /**
     * @param mixed $response
     * @return void
     */
    public static function init($response)
    {
        $self = static::getInstance();
        $self->response = $response;
    }
}
