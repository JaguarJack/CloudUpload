<?php

namespace Lizyu\Icloud;

use Lizyu\Icloud\HttpClient\Client;
use Lizyu\Icloud\Auth\QiniuAuth as QinuAuth;
use Lizyu\Icloud\Auth\UpyunAuth as UpyunAuth;
use Lizyu\Icloud\Traits\Utility;
use Lizyu\Icloud\Exceptions\NotFoundException;

abstract class IcloudAbstract
{
    use Utility;
    
    const GET_METHOD    = 'GET';
    const POST_METHOD   = 'POST';
    const DEL_METHOD    = 'DELETE';
    const PUT_METHOD    = 'PUT';
    
    protected $api;
    protected $host;
    protected $namespace;
    protected $reponse;
    
    public function __construct()
    {
        $this->api   = config('icloud.api');
        $this->host  = config('icloud.host');
    }
    
    /**
     * @description:获取apiURI
     * @author wuyanwen(2018年3月14日)
     * @param unknown $key
     * @return string
     */
    protected function getApiUri($key, $host = 'rs', bool $isHttps = false)
    {
        if (!array_key_exists($host, $this->host)) {
            throw NotFoundException::NotFoundKey("Host Not Found In Config File");
        }

        if ($key && !isset($this->api[$key])) {
            throw NotFoundException::NotFoundKey("Key '{$key}' Not Found In This Api Array");
        }
        
        return !$key ? self::getHost($host, $isHttps) : self::getHost($host, $isHttps) . $this->api[$key];
    }
    
    /**
     * @description:指定目标资源空间与目标资源名编码
     * @author wuyanwen(2018年3月14日)
     * @param string $bucket
     * @param string $resourceName
     */
    protected function encodedEntry(string $bucket, string $resourceName)
    {
        return self::urlSafeBase64Encode(sprintf('%s:%s', $bucket, $resourceName));
    }
    
    public function send(string $uri, string $method, array $options = [])
    {
        $client         = new Client;
        $client->uri    = $uri;
        $client->method = $method;
        $headers        = config('icloud.dirver') == 'qiniu' ? QinuAuth::authorization($uri) : UpyunAuth::authorization(parse_url($uri)['path'], $method);
        $client->params = array_merge_recursive(['headers' => $headers], $options);
        return $client->send();
    }
    
    /**
     * @description:获取上传凭证
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月17日
     * @param string $bucket
     * @param string $key
     * @param int $expires
     * @param string $policy
     * @param bool $strictPolicy
     * @return string
     */
    protected function UploadToken(
        string $bucket,
        string $key = '',
        int $expires = 3600,
        string $policy = '',
        bool $strictPolicy = true
    ){
        return QinuAuth::uploadToken($bucket);
    }
}