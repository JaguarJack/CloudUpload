<?php

namespace Lizyu\Icloud\Qiniu;

use Lizyu\Icloud\IcloudAbstract;
use GuzzleHttp\Psr7\Stream;
use Lizyu\Icloud\Exceptions\NotFoundException;
use finfo;

final class Object extends IcloudAbstract
{
    const BLOCK_SIZE = 4 * 1024 * 1024;
    
    /**
     * @description:列出空间所有资源
     * @author wuyanwen(2018年3月14日)
     * @param string $bucket
     * @param string $marker
     * @param number $limit
     * @param string $prefix
     * @param string $delimiter
     */
    public function list(string $bucket, string $marker = '', int $limit = 10, string $prefix = '', string $delimiter = '')
    {
        $uri = sprintf($this->getApiUri('list', 'rsf'), $bucket, $marker, $limit, $prefix, $delimiter);
        
        return $this->send($uri, self::GET_METHOD );
    }
    
    /**
     * @description:获取资源原信息
     * @author wuyanwen(2018年3月14日)
     * @param string $bucket
     * @param string $resourceName
     */
    public function stat(string $bucket, string $resourceName)
    {
        //1372-the-dawn-of-hope-tomasz-chistowski.jpg
        
        $encodedEntryUri = $this->encodedEntry($bucket, $resourceName);
        
        $uri = sprintf($this->getApiUri('stat'), $encodedEntryUri);
        
        return $this->send($uri, self::GET_METHOD );
    }
    
    /**
     * @description:将资源从一个空间移动到另一个空间， 该操作不支持跨账号操作, 不支持跨区域操作
     * @author wuyanwen(2018年3月14日)
     * @param string $localBucket
     * @param string $destBucket
     * @param string $localResourceName
     * @param string $destResourceName
     */
    public function move(string $localBucket, string $destBucket, string $localResourceName, string $destResourceName = '')
    {
        $encodedEntryURISrc  = $this->encodedEntry($localBucket, $localResourceName);
        $encodedEntryURIDest = $this->encodedEntry($destBucket, $destResourceName ? : $localResourceName);
        
        $uri = sprintf($this->getApiUri('move'), $encodedEntryURISrc, $encodedEntryURIDest);
        
        return $this->send($uri, self::POST_METHOD );
    }
    
    /**
     * @description:将资源从一个空间复制到另一个空间， 该操作不支持跨账号操作, 不支持跨区域操作
     * @author wuyanwen(2018年3月14日)
     * @param string $localBucket
     * @param string $destBucket
     * @param string $localResourceName
     * @param string $destResourceName
     */
    public function copy(string $localBucket, string $destBucket, string $localResourceName, string $destResourceName = '')
    {
        $encodedEntryURISrc  = $this->encodedEntry($localBucket, $localResourceName);
        $encodedEntryURIDest = $this->encodedEntry($destBucket, $destResourceName ? : $localResourceName);
        
        $uri = sprintf($this->getApiUri('copy'), $encodedEntryURISrc, $encodedEntryURIDest);
        
        return $this->send($uri, self::POST_METHOD);
    }
    
    /**
     * @description:删除空间指定资源
     * @author wuyanwen(2018年3月14日)
     * @param string $bucket
     * @param string $resourceName
     */
    public function delete(string $bucket, string $resourceName)
    {
        $encodedEntryUri = $this->encodedEntry($bucket, $resourceName);
        
        $uri = sprintf($this->getApiUri('delete'), $encodedEntryUri);
        
        return $this->send($uri, self::POST_METHOD);
    }
    
    /**
     * @description:抓取远程IMG到指定空间
     * @author wuyanwen(2018年3月14日)
     * @param unknown $remoteImgUri
     * @param unknown $destBucket
     */
    public function fetch(string $remoteImgUri, string $destBucket)
    {
        $imgEncodedUri   = self::urlSafeBase64Encode($remoteImgUri);
        $encodedEntryUri = self::urlSafeBase64Encode($destBucket);
        
        $uri = sprintf($this->getApiUri('fetch', 'iovip'), $imgEncodedUri, $encodedEntryUri);
        
        return $this->send($uri, self::POST_METHOD);
    }
    
    /**
     * @description:批量操作
     * @author wuyanwen(2018年3月14日)
     * @param array $batchOptions
     * @说明
     * 数组格式
     * [
     *    stat   => ['bucket', 'resourceName']
     *    delete => ['bucket', 'resourceName']
     *    move   => ['localbucket', 'destbucket', 'resourceName', 'destResourceName'(可不写)]
     *    copy   => ['localbucket', 'destbucket', 'resourceName', 'destResourceName'(可不写)]
     * ]
     */
    public function batch(array $batchOptions)
    {
        $requestParams = '';
        
        foreach ($batchOptions as $option => $param)
        {
            if ($option === 'stat' || $option === 'delete') {
                $requestParams .= sprintf('op=/%s/%s&', $option, $this->encodedEntry($param[0], $param[1]));
            } else if($option === 'move' || $option === 'copy') {
                $encodedEntryURISrc  = $this->encodedEntry($param[0], $param[2]);
                $encodedEntryURIDest = $this->encodedEntry($param[1], count($param) >= 4 ? $param[3] : $param[2]);
                $requestParams .= sprintf('op=/%s/%s/%s&', $option, $encodedEntryURISrc, $encodedEntryURIDest);
            } else {
                continue;
            }
        }
        
        $uri = sprintf('%s?%s', $this->getApiUri('batch'), $requestParams);
        
        return $this->send($uri, self::POST_METHOD);
    }
    
    /**
     * @description:镜像资源更新
     * @author wuyanwen(2018年3月14日)
     * @param string $bucket
     * @param string $resource
     */
    public function prefetch(string $bucket, string $resourceName)
    {
        $encodedEntryUri = $this->encodedEntry($bucket, $resourceName);
        
        $uri = sprintf($this->getApiUri('prefetch', 'iovip'), $encodedEntryUri);
        
        return $this->send($uri, self::POST_METHOD);
    }
    
    /**
     * @description:HTTP直传文件
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月17日
     * @param string $bucket 空间名称
     * @param unknown $file 资源FILE
     * @param array $params 直传可选参数 => https://developer.qiniu.com/kodo/api/1312/upload
     */
    public function uploadFile(string $bucket, $file, array $params = [])
    {
        if (!is_resource($file)) {
            throw new \Exception('$file Must Be Resource Type');
        }
        
        $uri = $this->getApiUri('', 'up');
        $stream = new Stream($file);
        //判断如果文件大于4M则使用分块上传
        if ($stream->getSize() > self::BLOCK_SIZE) {
            return $this->uploadFileByBlocks($bucket, $file);
        }
        
        $filename = md5(basename($stream->getMetadata('uri')) . time());
        $uploadToken = $this->UploadToken($bucket);
        
        $options['multipart'] = [
            ['name' => 'key', 'contents' =>  basename($stream->getMetadata('uri'))],
            ['name' => 'file', 'contents' => $stream, 'filename' => basename($stream->getMetadata('uri'))],
            ['name' => 'token', 'contents' => $uploadToken],
            ['name' => 'crc32', 'contents' => self::crc32_data($stream)],
            ['name' => 'Content-Type', 'contents' => 'application/octet-stream'],
        ];
        
        if (!empty($params)) {
            $options['multipart'] = array_merge($params, $options['multipart']);
        }
        
        return $this->send($uri, self::POST_METHOD, $options);
    }
    
   
    /**
     * @description:创建块
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月18日
     * @param unknown $nextChunkOffset
     */
    protected function uploadFileByBlocks(string $bucket, $file)
    {
        //需要安装fileinfo扩展
        if (!extension_loaded('fileinfo')) {
            throw NotFoundException::NotFoundExtension('PHPExtension Fileinfo Not Found, Please Install It First');
        }
        $stream = new Stream($file);
        $filezie = $stream->getSize();
        //保存ctx值， 用于后续合并文件
        $ctxArr = [];
        //已上传文件大小
        $uploadSize = 0;
        while ($uploadSize < $filezie) {
            //剩余文件大小
            $remainsize = $filezie - $uploadSize;
            //需要读取的文件大小
            $needReadSize = $remainsize > self::BLOCK_SIZE ? self::BLOCK_SIZE : $remainsize;
            $content = $stream->read($needReadSize);
            //创建块并且上传第一个片
            $options['body'] = $content;
            $headers = [
                'Content-Type'   => 'application/octet-stream',
                'Content-Length' => $needReadSize,
            ];
            $options['headers']  = $headers;
            $uri = sprintf($this->getApiUri('mkblk', 'up'), $needReadSize);
            $response = $this->send($uri, self::POST_METHOD, $options);
            $data = json_decode($response->getBody()->getContents(), true);
            
            array_push($ctxArr, $data['ctx']);
            $uploadSize += $needReadSize;
        }
        
        return $this->mkfile($stream, $bucket, $ctxArr);
    }
    
    /**
     * @description:创建文件
     * @author wuyanwen(2018年3月19日)
     * @param Stream $stream
     * @param string $bucket
     * @param array $ctx
     */
    protected function mkfile(Stream $stream, string $bucket, array $ctx)
    {
        $file     = $stream->getMetadata('uri');
        $key      = self::urlSafeBase64Encode(sprintf('%s', basename($file)));
        $mimetype = (new finfo(FILEINFO_MIME_TYPE))->file($file);
        $filesize = $stream->getSize();
        $userVar  = md5(time());
        
        $options['headers'] = ['Authorization' => 'UpToken ' . $this->UploadToken($bucket, basename($file))];
        $options['body'] = implode(',', $ctx);
        
        $uri = sprintf($this->getApiUri('mkfile', 'up'), $filesize, $key, self::urlSafeBase64Encode($mimetype), self::urlSafeBase64Encode($userVar));
        return $this->send($uri, self::POST_METHOD, $options);
    }    
}