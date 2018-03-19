<?php

namespace Lizyu\Icloud;

use Illuminate\Support\ServiceProvider;
use Lizyu\Icloud\Icloud;
use Lizyu\Icloud\Qiniu\BucketManager as QiniuBucket;
use Lizyu\Icloud\Qiniu\Object;
use Lizyu\Icloud\Upyun\BucketManager as UpyunBucket;

class IcloudServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //"Lizyu\\Icloud\\":"package/lizyu/icloud/src/",Lizyu\Test\IcloudServiceProvider::class,
        $this->publishConfig();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerQiniuService();
        $this->registerQiniuService();
        $this->app->bind('icloud', function($app){
            return new Icloud($app);
        });
    }
    
    /**
     * @description:注册七牛接口服务
     * @author wuyanwen(2018年3月19日)
     */
    protected function registerQiniuService()
    {
        $this->app->bind('qiniuBucket', function(){
            return new QiniuBucket();
        });
            
        $this->app->bind('object', function(){
            return new Object();
        });
        
       $this->app->tag(['qiniuBucket', 'object'], 'qiniu');
    }
    
    /**
     * @description:注册又拍云接口服务
     * @author wuyanwen(2018年3月19日)
     */
    protected function registerUpyunService()
    {
       
        $this->app->bind('upyunBucket', function(){
            return new UpyunBucket();
        });
        
        $this->app->tag(['upyunBucket'], 'upyun');
    }
    
    protected function publishConfig()
    {
        $this->publishes([
            __DIR__  . '/../config/icloud.php' => $this->app->configPath() . '/icloud.php',
        ], 'icloud.config');
    }
}
