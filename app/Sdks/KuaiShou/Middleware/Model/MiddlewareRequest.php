<?php

namespace App\Sdks\KuaiShou\Middleware\Model;


use App\Sdks\KuaiShou\Kernel\BaseObject;
use App\Sdks\KuaiShou\KuaiShou;


class MiddlewareRequest extends BaseObject
{
    /**
     * @var
     */
    public $app;

    /**
     * Api entity class
     * @var string
     */
    protected $apiEntityName;

    /**
     * Method name
     * @var string
     */
    protected $apiMethod;

    /**
     * Method params
     * @var array
     */
    protected $apiMethodParams;

    /**
     * @return KuaiShou
     */
    public function getApp(): KuaiShou
    {
        return $this->app;
    }

    /**
     * @param KuaiShou $app
     */
    public function setApp(KuaiShou $app)
    {
        $this->app = $app;
    }

    /**
     * @return string
     */
    public function getApiEntityName(): string
    {
        return $this->apiEntityName;
    }

    /**
     * @param string $apiEntityName
     */
    public function setApiEntityName(string $apiEntityName)
    {
        $this->apiEntityName = $apiEntityName;
    }

    /**
     * @return string
     */
    public function getApiMethod(): string
    {
        return $this->apiMethod;
    }

    /**
     * @param string $apiMethod
     */
    public function setApiMethod(string $apiMethod)
    {
        $this->apiMethod = $apiMethod;
    }

    /**
     * @return array
     */
    public function getApiMethodParams(): array
    {
        return $this->apiMethodParams;
    }

    /**
     * @param array $apiMethodParams
     */
    public function setApiMethodParams(array $apiMethodParams)
    {
        $this->apiMethodParams = $apiMethodParams;
    }

    /**
     * @param KuaiShou $app
     * @param string $className Api entity class name
     * @param string $method
     * @param mixed $params
     * @return MiddlewareRequest
     */
    public static function init(KuaiShou $app, string $className, string $method, $params): MiddlewareRequest
    {
        $self = static::getInstance();
        $self->setApp($app);
        $self->setApiEntityName($className);
        $self->setApiMethod($method);
        $self->setApiMethodParams($params);
        return $self;
    }
}
