<?php
/**
 * 资源空间操作
 */
namespace Lizyu\Icloud\Upyun;

use Lizyu\Icloud\IcloudAbstract;
use GuzzleHttp\Psr7\Stream;
use function GuzzleHttp\Psr7\stream_for;

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
    
    /**
     * @description:上传文件
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月17日
     * @param string $bucket
     * @param int $contentLenght
     * @param array $options => 参考http://docs.upyun.com/api/rest_api/#_2
     */
    public function uploadFile(string $bucket, string $fileDir, $locationFile, array $options = [])
    {
        if (!is_resource($locationFile)) {
            throw new \Exception('$localfile Must Be Resource Type', 500);
        }
        $stream   = stream_for($locationFile);
        #上传文件大于限制文件大小， 则断点续传
        if ( $stream->getSize() > config('icloud.filesize') ) {
            return $this->sliceUpload($bucket, $fileDir, $stream, $options);
        }
        
        $filename = basename($stream->getMetadata('uri'));
        $uri = sprintf($this->getApiUri('upload_file', 'v0'), $bucket, $fileDir . $filename);
         
        if (!empty($options)) $options['headers'] = $options;
        $options['headers'] = ['Content-Length' => $stream->getSize()];
        $options['body']    = $stream;
        
        $response = $this->send($uri, self::PUT_METHOD, $options);
        dd($response);
    }

    
    /**
     * @description:断点续传
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月17日
     * @param string $bucket
     * @param string $fileDir
     * @param Stream $stream
     * @param array $option
     */
    public function sliceUpload(string $bucket, string $fileDir, $locationFile, array $option = [])
    {
        if (!is_resource($locationFile)) {
            throw new \Exception('$localfile Must Be Resource Type', 500);
        }
        
        $stream   = stream_for($locationFile);
        
        if (!empty($options)) $options['headers'] = $options;
        #params
        $params = [
            'Content-Length'       => 452,
            'X-Upyun-Multi-Stage'  => 'initiate',
           // 'X-Upyun-Multi-Length' => 452,
            //'X-Upyun-Multi-Type'   => 'application/octet-stream',
        ];
        $options['headers'] = $params;
        $uri = sprintf($this->getApiUri('upload_file', 'v0'), $bucket, $fileDir . basename($stream->getMetadata('uri')));

        $response = $this->send($uri, self::PUT_METHOD, $options);
        dd($response);
    }
    
    
    
}