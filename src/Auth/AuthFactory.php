<?php

namespace Lizyu\Icloud\Auth;

class AuthFactory
{
    /**
     * @description:创建对象
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月19日
     * @param unknown $name
     * @param unknown ...$argument
     * @return unknown
     */
    public static function create(string $name, ...$argument)
    {
        $auth = '\\Lizyu\\Icloud\\Auth\\' . ucwords(config('icloud.dirver')) . 'Auth';

        return $auth::$name(...$argument);
    }
    
    /**
     * @description:请求对象方法
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月19日
     * @param string $name
     * @param unknown $argument
     * @return \Lizyu\Icloud\Auth\unknown
     */
    public static function __callstatic(string $name, $argument)
    {
        return self::create($name, ...$argument);
    }
}