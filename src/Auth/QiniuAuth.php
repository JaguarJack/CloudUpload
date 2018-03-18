<?php

namespace Lizyu\Icloud\Auth;

use Lizyu\Icloud\Traits\Utility;

final class QiniuAuth
{
    use Utility;
    /**
     * @description:管理凭证授权
     * @author wuyanwen(2018年3月13日)
     * @param unknown $url
     * @param unknown $body
     * @param unknown $contentType
     * @return string[]
     */
    public static function authorization(string $uri, string $body = '', $contentType = 'application/x-www-form-urlencoded')
    {
        return ['Authorization' => sprintf('QBox %s', self::getAccessToken($uri, $body, $contentType))];
    }
    
    /**
     * @description:获取管理Token
     * @author wuyanwen(2018年3月13日)
     * @param unknown $urlString
     * @param unknown $body
     * @param unknown $contentType
     * @return string
     */
    protected static function getAccessToken(string $urlString, string $body, string $contentType = '')
    {
        $appKey = config('icloud.qiniu.qiNiuKey');
        $appSecret = config('icloud.qiniu.qiNiuSecret');
        
        $url = parse_url($urlString);
        $data = '';
        if (array_key_exists('path', $url)) {
            $data = $url['path'];
        }
        if (array_key_exists('query', $url)) {
            $data .= '?' . $url['query'];
        }
        $data .= "\n";
        if ($body && $contentType === 'application/x-www-form-urlencoded') {
            $data .= $body;
        }
        $data = hash_hmac('sha1', $data, $appSecret, true);
        $encodedSign = self::urlSafeBase64Encode($data);
        $accessToken = sprintf('%s:%s', $appKey, $encodedSign);
        return $accessToken;
    }
    
    /**
     * @description:获取上传凭证Token
     * @author wuyanwen(2018年3月13日)
     */
    public static function uploadToken(
        string $bucket, 
        string $key = '', 
        int $expires = 3600, 
        string $policy = '',
        bool $strictPolicy = true
     ){
        $appKey = config('icloud.qiniu.qiNiuKey');
        $appSecret = config('icloud.qiniu.qiNiuSecret');
        
        $scope = $key ? sprintf('%s:%s', $bucket, $key) : $bucket;
        $deadline = time() + $expires;
        $args = self::copyPolicy($args, $policy, $strictPolicy);
        
        $args['scope'] = $scope;
        $args['deadline'] = $deadline;
        
        $encodedPutPolicy = self::urlSafeBase64Encode(json_encode($args));
        $sign             = hash_hmac('sha1', $encodedPutPolicy, $appSecret, true);
        $encodedSign      = self::urlSafeBase64Encode($sign);
        
        return sprintf('%s:%s:%s', $appKey, $encodedSign, $encodedPutPolicy);
    }
    
    private static function copyPolicy(&$policy, $originPolicy, $strictPolicy)
    {
        if (!$originPolicy) {
            return [];
        }
        
        $policyFields = config('qiniu.policyFields');
        
        foreach ($originPolicy as $key => $value) {
            if (!$strictPolicy || in_array((string)$key, $policyFields, true)) {
                $policy[$key] = $value;
            }
        }
        
        return $policy;
    }
    
    /**
     * @description:downLoad Token
     * @author wuyanwen(2018年3月14日)
     * @param string $uri
     * @param int $expires
     * @return string
     */
    public static function dowmloadToken(string $uri, int $expires = 3600)
    {
        $appSecret = config('icloud.qiniu.qiNiuSecret');
        
        $uri = sprintf('%s?e=%s', $uri, time() + $expires);
        
        $sign = hash_hmac('sha1', $uri, $this->appSecret, true);
        
        $encodedSign = self::urlSafeBase64Encode($sign);
        
        return sprintf('%s:%s', $this->appKey, $encodedSign);
        
    }
}