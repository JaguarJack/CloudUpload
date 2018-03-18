<?php

namespace Lizyu\Icloud\Common;

use GuzzleHttp\Client as HttpClient;

class Client implements \ArrayAccess
{
    protected $params = [];
    protected $client;
    
    public function __construct(HttpClient $client)
    {
        $this->client = $client;    
    }
    
    /**
     * @description:发送请求
     * @author wuyanwen(2018年3月15日)
     * @return unknown
     */
    public function send()
    {
        return $this->client->request($this->method, $this->uri, $this->params);
    }
    
    /**
     * @description:Magic Method
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月13日
     * @param string $key
     * @param unknown $value
     */
    public function __set(string $key, $value)
    {
        $this->$key = $value;
    }
    
    public function offsetSet($offset, $value)
    {
        $this->params[$offset] = $value;
    }
    
    public function offsetGet($offset)
    {
        return $this->params[$offset];
    }
    
    public function offsetExists($offset)
    {
        return isset($this->params[$offset]);
    }
    
    public function offsetUnset($offset)
    {
        unset($this->params[$offset]);
    }
}