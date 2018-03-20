<?php

namespace Lizyu\Icloud\Upyun;

use Lizyu\Icloud\Exceptions\NotFoundException;
use Lizyu\Icloud\IcloudAbstract;
use GuzzleHttp\Psr7\Stream;
use finfo;

class ResourceManager extends IcloudAbstract
{
    const BLOCK_SIZE   = 1024 * 1024;
    const SUCCESS_CODE = 204;
    
    protected $bucket;
    protected $fileDir;
    protected $stream;
    protected $options;
    protected $filename;
    //本次上传任务的标识，是初始化断点续传任务时响应信息中的
    protected $multiuuid;
    //指定此次分片的唯一 ID，应严格等于上一次请求返回的
    protected $nextpartid;
    
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
        $stream   = new Stream($locationFile);
        
        $this->bucket  = $bucket;
        $this->fileDir = $fileDir;
        $this->stream  = $stream;
        $this->options = $options;
        $this->filename = basename($stream->getMetadata('uri'));
        #上传文件大于限制文件大小， 则断点续传
        if ( $stream->getSize() > self::BLOCK_SIZE ) {
            return $this->uploadComplete();
        }
        
        $uri = sprintf($this->getApiUri('upload_file', 'v0'), $this->bucket, $this->fileDir . $this->filename);
        
        if (!empty($this->options)) $options['headers'] = $this->options;
        $options['headers'] = ['Content-Length' => $stream->getSize()];
        $options['body']    = $this->stream;
        
        return $this->send($uri, self::PUT_METHOD, $this->options);
    }
    
    /**
     * @description:初始化断点续传
     * @author wuyanwen(2018年3月20日)
     */
    protected function initUpload()
    {
        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($this->stream->getMetadata('uri'));
        
        $headrs = [
            'x-upyun-multi-stage'  => 'initiate',
            'x-upyun-multi-length' => $this->stream->getSize(),
            'x-upyun-multi-type'   => $mimeType ? : 'application/octet-stream',
        ];
        $this->options['headers']  = $headrs;
        
        $uri = sprintf($this->getApiUri('upload_file', 'v0'), $this->bucket, $this->fileDir . $this->filename);
        $response = $this->send($uri, self::PUT_METHOD, $this->options);
        
        if ( !($response->getStatusCode() == self::SUCCESS_CODE) ) {
            throw new \Exception('Failed To Respond');
        }
        
        $headers = $response->getHeaders();
            
        if (!isset($headers['x-upyun-multi-uuid'])) {
            throw NotFoundException::NotFoundKey('Response Headers Not Found Key "x-upyun-multi-uuid"');
        }
        
        if (!isset($headers['x-upyun-next-part-id'])) {
            throw NotFoundException::NotFoundKey('Response Headers Not Found Key "x-upyun-next-part-id"');
        }
                
        $this->multiuuid  = $headers['x-upyun-multi-uuid'];
        $this->nextpartid = $headers['x-upyun-next-part-id'];
    }
    
    /**
     * @description:上传分块
     * @author wuyanwen(2018年3月20日)
     * @return null
     */
    protected function uploading()
    {
        $uploadSize = 0;
        
        $filesize   = $this->stream->getSize();
        while ($uploadSize < $filesize) {
            //剩余文件大小
            $remainsize = $filesize - $uploadSize;
            //需要读取的文件大小
            $needReadSize = $remainsize > self::BLOCK_SIZE ? self::BLOCK_SIZE : $remainsize;
            $content = $this->stream->read($needReadSize);
            
            $headrs = [
                'x-upyun-multi-stage' => 'upload',
                'x-upyun-multi-uuid'  => $this->multiuuid,
                'x-upyun-part-id'     => $this->nextpartid,
            ];
            
            $this->options['body']    = $content;
            $this->options['headers'] = $headrs;
            $uri = sprintf($this->getApiUri('upload_file', 'v0'), $this->bucket, $this->fileDir . $this->filename);
            $response = $this->send($uri, self::POST_METHOD, $this->options);
            if ( !($response->getStatusCode() == self::SUCCESS_CODE) ) {
                throw new \Exception('Failed To Respond');
            }
            $headers = $response->getHeaders();
            if (!isset($headers['x-upyun-multi-uuid'])) {
                throw NotFoundException::NotFoundKey('Response Headers Not Found Key "x-upyun-multi-uuid"');
            }
            if (!isset($headers['x-upyun-next-part-id'])) {
                throw NotFoundException::NotFoundKey('Response Headers Not Found Key "x-upyun-next-part-id"');
            }
            $this->multiuuid  = $headers['x-upyun-multi-uuid'];
            $this->nextpartid = $headers['x-upyun-next-part-id'];
            $uploadSize += $needReadSize;
        }
    }
    
    /**
     * @description:完成上传
     * @author wuyanwen(2018年3月20日)
     */
    protected function uploadComplete()
    {
        //初始化
        $this->initUpload();
        //上传
        $this->uploading();
        //合并完成上传        
        $headrs = [
            'x-upyun-multi-stage' => 'complete',
            'x-upyun-multi-uuid'  => $this->multiuuid,
        ];
        $this->options['headers'] = $headrs;
        
        $uri = sprintf($this->getApiUri('upload_file', 'v0'), $this->bucket, $this->fileDir . $this->filename);
        return $this->send($uri, self::POST_METHOD, $this->options);
    }
}