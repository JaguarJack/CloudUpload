<?php

namespace Lizyu\Icloud\Traits;

Trait Utility
{
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
    
    /**
     * @description:crc32校验
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月18日
     * @param string $data
     * @return string
     */
    public static function crc32_data(string $data)
    {
        $hash = hash('crc32b', $data);
        $array = unpack('N', pack('H*', $hash));
        return sprintf('%u', $array[1]);
    }
}