<?php

namespace Lizyu\Icloud\Common;

use Lizyu\Icloud\Common\Utility;

final class Auth
{
    /* qiniu key */
    private $appKey;
    /* qiniu Secret */
    private $appSecret;
    
    public function __construct()
    {
        $this->appKey = config('icloud.qiniu.qiNiuKey');
        $this->appSecret = config('icloud.qiniu.qiNiuSecret');
    }
    
    /**
     * @description:管理凭证授权
     * @author wuyanwen(2018年3月13日)
     * @param unknown $url
     * @param unknown $body
     * @param unknown $contentType
     * @return string[]
     */
    public function authorization(string $uri, string $body = '', $contentType = 'application/x-www-form-urlencoded')
    {
        return ['Authorization' => sprintf('QBox %s', $this->getAccessToken($uri, $body, $contentType))];
    }
    
    /**
     * @description:获取管理Token
     * @author wuyanwen(2018年3月13日)
     * @param unknown $urlString
     * @param unknown $body
     * @param unknown $contentType
     * @return string
     */
    protected function getAccessToken(string $urlString, string $body, string $contentType = '')
    {
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
        $data = hash_hmac('sha1', $data, $this->appSecret, true);
        $encodedSign = Utility::urlSafeBase64Encode($data);
        $accessToken = sprintf('%s:%s', $this->appKey, $encodedSign);
        return $accessToken;
    }
    
    /**
     * @description:获取上传凭证Token
     * @author wuyanwen(2018年3月13日)
     */
    public function uploadToken(string $bucket, string $key = '', int $expires = 3600, string $policy = '', bool $strictPolicy = true)
    {
        $scope = $key ? sprintf('%s:%s', $bucket, $key) : $bucket;
        $deadline = time() + $expires;
        
        $args = $this->copyPolicy($args, $policy, $strictPolicy);
        $args['scope'] = $scope;
        $args['deadline'] = $deadline;
        
        $encodedPutPolicy = Utility::urlSafeBase64Encode(json_encode($args));
        $sign             = hash_hmac('sha1', $encodedPutPolicy, $this->appSecret, true);
        $encodedSign      = Utility::urlSafeBase64Encode($sign);
        
        return sprintf('%s:%s:%s', $this->appKey, $encodedSign, $encodedPutPolicy);
    }
    
    private function copyPolicy(&$policy, $originPolicy, $strictPolicy)
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
    public function dowmloadToken(string $uri, int $expires = 3600)
    {
        $expires = time() + $expires;
        $uri = sprintf('%s?e=%s', $uri, $expires);
        
        $sign = hash_hmac('sha1', $uri, $this->appSecret, true);
        $encodedSign = Utility::urlSafeBase64Encode($sign);
        
        return sprintf('%s:%s', $this->appKey, $encodedSign);
        
    }
}