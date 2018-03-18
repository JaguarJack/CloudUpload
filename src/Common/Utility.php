<?php

namespace Lizyu\Icloud\Common;

final class Utility
{
    protected static $params = [];
    
    /**
     * @description:URLBase64加密
     * @author wuyanwen(2018年3月13日)
     * @param string $string
     * @return mixed
     */
    public static function urlSafeBase64Encode(string $string)
    {
        return str_replace(['+','/'], ['-','_'], base64_encode($string));
    }
    
   /**
    * @description:获取七牛接口HOST
    * @author wuyanwen(2018年3月13日)
    * @param string $type
    * @param bool $isHttps
    * @return string
    */
    public static function getHost(string $host = 'rs', bool $isHttps = false)
    {
        return $isHttps ? 'https://' : 'http://' . config('icloud.host.'. strtolower($host));
    }
}