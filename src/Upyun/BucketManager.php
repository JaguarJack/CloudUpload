<?php
/**
 * 资源空间操作
 */
namespace Lizyu\Icloud\Upyun;

use Lizyu\Icloud\IcloudAbstract;
use GuzzleHttp\Psr7\Stream;
use Lizyu\Icloud\Exceptions\NotFoundException;

final class BucketManager extends IcloudAbstract
{
    /**
     * @description:创建文件夹
     * @author wuyanwen(2018年3月15日)
     * @param string $bucket
     * @param string $folder
     */
    public function create(string $bucket, string $directory)

    {
        $uri = sprintf($this->getApiUri('folder', 'v0'),  $bucket . $directory );

        $response = $this->send($uri, self::POST_METHOD);
        dd($response);
    }
    
    /**
     * @description:删除空间
     * @author wuyanwen(2018年3月13日)
     * @param unknown $bucket (bucket名称)
     */
    public function drop(string $bucket, string $directory)
    {
        $uri = sprintf($this->getApiUri('drop_dir', 'v0'), $bucket . $directory );

        $response = $this->send($uri, self::DEL_METHOD);
        dd($response);
        
    }
    
    /**
     * @description:获取文件列表
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月17日
     * @param string $bucket
     * @param string $directory
     * @param array $options ['x-list-iter' => '分页开始位置', 'x-list-limit' => '获取文件数量', 'x-list-order' => '排序' ]
     */
    public function list(string $bucket, string $directory, array $options = ['x-list-limit' => 1])
    {
        $uri = sprintf($this->getApiUri('dir_list', 'v0'), $bucket . $directory );
        $response = $this->send($uri, self::GET_METHOD, $options);
        dd($response->getHeaders());
    }
    
    /**
     * @description:获取服务容量
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月17日
     * @param string $bucket
     */
    public function usage(string $bucket)
    {
        $uri = sprintf($this->getApiUri('usage', 'v0'), $bucket );
        $response = $this->send($uri, self::GET_METHOD);
        dd($response->getBody());
    }
    
    /**
     * @description:删除文件
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月17日
     * @param string $bucket
     * @param string $fileDir
     */
    public function deleteFile(string $bucket, string $fileDir)
    {
        $uri = sprintf($this->getApiUri('del_file', 'v0'), $bucket, $fileDir );
        $response = $this->send($uri, self::DEL_METHOD);
        dd($response);
    }
    
    /**
     * @description:下载文件
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月17日
     * @param string $bucket
     * @param string $fileDir
     */
    public function downloadFile(string $bucket, string $fileDir, string $localPath = '')
    {
        $uri = sprintf($this->getApiUri('down_file', 'v0'), $bucket, $fileDir );
        
        $response = $this->send($uri, self::GET_METHOD, ['stream' => true]);
        
        dd($response);
    }
}