<?php

namespace Lizyu\Icloud\Upyun;

use Lizyu\Icloud\HttpClient\Client;
use Lizyu\Icloud\Upyun\Auth;
use Lizyu\Icloud\Utility;
use Lizyu\Icloud\Exceptions\NotFoundException;

abstract class UpyunAbstract
{
    const GET_METHOD  = 'GET';
    const POST_METHOD = 'POST';
    protected $client;
    protected $auth;
    protected $utility;
    protected $api = [
            'folder' => '/%s/',
    ];
    public function __construct(Auth $auth, Client $client, Utility $utility)
    {
        $this->auth    = $auth;
        $this->client  = $client;
        $this->utility = $utility;
    }
    
    /**
     * @description:获取apiURI
     * @author wuyanwen(2018年3月14日)
     * @param unknown $key
     * @return string
     */
    protected function getApiHost($host = 'v0', bool $isHttps = false)
    {
        if (!array_key_exists($host, config('icloud.upyun.host'))) {
            throw NotFoundException::NotFoundKey("Host Not Found In Config File");
        }
        
        return $this->utility::getUpyunHost($host, $isHttps);
    }
    
    public function send($apiUriKey, $method, $host = 'v0', $isHttp = false)
    {
        $this->client->uri    = $this->getApiHost($host = 'v0') . $this->api[$apiUriKey];
        $this->client->method = $method;
        $this->client['headers'] = $this->auth->authorization($this->api[$apiUriKey]);
        return $this->client->send();
    }
    
}