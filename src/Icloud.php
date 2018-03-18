<?php

namespace Lizyu\Icloud;

use Illuminate\Foundation\Application;
use Lizyu\Icloud\Exceptions\NotFoundException;

class Icloud
{
    protected $app;
    
    public function __construct(Application $app)
    {
        $this->app    = $app;
    }
    
    public function __call($method, $argument)
    {
        $dirver = config('icloud.dirver');
        
        foreach ($this->app->tagged($dirver) as $api) {
            if (method_exists($api, $method)) {
                return $api->$method(...$argument);
            }
        }
        
        throw NotFoundException::NotFoundMethod("The Driver {$this->dirver} Dont Provider Method {$method}");
    }
    
   /**
    * @description:切换驱动
    * @author: wuyanwen <wuyanwen1992@gmail.com>
    * @date:2018年3月16日
    * @param string $dirver
    */
    public function __invoke(string $dirver)
    {
        config(['icloud.dirver' => $dirver]);
        
        return $this;
    }
}