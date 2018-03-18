<?php

namespace Lizyu\Icloud\Common;

use Lizyu\Icloud\Common\Client;
use Lizyu\Icloud\Common\Auth;
use Lizyu\Icloud\Common\Utility;
use Lizyu\Icloud\Exceptions\NotFoundException;

abstract class IcloudAbstract
{
    const GET_METHOD    = 'GET';
    const POST_METHOD   = 'POST';
    protected $client;
    protected $auth;
    protected $utility;
    protected $api;
    protected $host;
    
    public function __construct(Auth $auth, Client $client, Utility $utility)
    {
        $this->auth    = $auth;
        $this->client  = $client;
        $this->utility = $utility;
        $this->api     = config('icloud.api');
        $this->host    = config('icloud.host');
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
        
        if (!isset($this->api[$key])) {
            throw NotFoundException::NotFoundKey("Key '{$key}' Not Found In This Api Array");
        }
        
        return $this->utility::getHost($host, $isHttps) . $this->api[$key];
    }
    
    /**
     * @description:指定目标资源空间与目标资源名编码
     * @author wuyanwen(2018年3月14日)
     * @param string $bucket
     * @param string $resourceName
     */
    protected function encodedEntry(string $bucket, string $resourceName)
    {
        return $this->utility::urlSafeBase64Encode(sprintf('%s:%s', $bucket, $resourceName));
    }
    
    public function send($uri, $method)
    {
        $this->client->uri    = $uri;
        $this->client->method = $method;
        $this->client['headers'] = $this->auth->authorization($uri);
        return $this->client->send();
    }
}