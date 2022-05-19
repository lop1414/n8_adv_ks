<?php


namespace App\Sdks\KuaiShou\Kernel;


class BaseObject
{
    /**
     * @var array
     */
    public static $instances = [];


    public static function getInstance(): BaseObject
    {
        if (empty(static::$instances[static::class])) {
            static::$instances[static::class] = new static();
        }
        return static::$instances[static::class];
    }
}
