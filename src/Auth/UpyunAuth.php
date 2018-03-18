<?php

namespace Lizyu\Icloud\Auth;

final class UpyunAuth
{
    /* 服务管理 操作员名称   */
    private $operator;
    /* 操作员对应的密码值   */
    private $password;
    
    public function __construct()
    {
        
    }
    
    /**
     * @description:upyun 签名
     * @author wuyanwen(2018年3月15日)
     * @param string $uri
     * @param string $method
     * @param string $contentMD5
     * @return string
     */
    public static function authorization(string $uri, string $method,  string $contentMD5= '')
    {
       $date = gmdate('D, d M Y H:i:s \G\M\T');
       
       $singArr = [$method, $uri, $date];

       if ($contentMD5) $singArr[] = $contentMD5;

       $sign = base64_encode(hash_hmac('sha1', implode('&', $singArr), md5(config('icloud.upyun.password')), true));
       
       return [
           'Authorization' => sprintf('UPYUN %s:%s', config('icloud.upyun.opreator'), $sign),
           'Date'          => $date,
       ];
    }
    
    /**
     * @description:获取token
     * @author wuyanwen(2018年3月15日)
     * @param string $method
     * @param int $expire 过期时间默认三个月
     * @param string $uriPrefix
     * @param string $uriPostfix
     */
    public static function uploadToken(string $method, int $expire = 3888000, string $uriPrefix = '', string $uriPostfix= '')
    {
        $operator = config('icloud.upyun.opreator');
        $password = config('icloud.upyun.password');
        
        $tokenArr = [$operator, $password, $method, $expire];
        
        if ($uriPrefix) $tokenArr[]  = $uriPrefix;
        if ($uriPostfix) $tokenArr[] = $uriPostfix;
        
        $token = base64_encode(hash_hmac('sha1',implode('&', $tokenArr) , $password, true));
        
        return sprintf('UPYUN %s:%s', $operator, $token);
    }
}