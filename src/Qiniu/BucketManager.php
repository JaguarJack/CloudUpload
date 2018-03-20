<?php
/**
 * 资源空间操作
 */
namespace Lizyu\Icloud\Qiniu;

use Lizyu\Icloud\IcloudAbstract;

final class BucketManager extends IcloudAbstract
{
    /**
     * @description:获取buckets列表
     * @author wuyanwen(2018年3月13日)
     */
    public function buckets()
    {
        $uri = $this->getApiUri('buckets');

        return $this->send( $uri, self::GET_METHOD );
    }
    
    /**
     * @description:创建bucket
     * @author wuyanwen(2018年3月13日)
     * @param unknown $bucket (bucket名称)
     * @param string $region (地区)[z0华东 z1华北 z2华南 na0北美 as0新加坡 ]
     */
    public function create(string $bucket, string $region)
    {
        $uri = sprintf($this->getApiUri('create_bucket'), self::urlSafeBase64Encode($bucket), $region);
        
        return $this->send( $uri, self::POST_METHOD );
    }
    
    /**
     * @description:删除空间
     * @author wuyanwen(2018年3月13日)
     * @param unknown $bucket (bucket名称)
     */
    public function drop(string $bucket)
    {
        $uri = sprintf($this->getApiUri('drop_bucket'), $bucket);
        
        return$this->send( $uri, self::POST_METHOD );
    }
    
    /**
     * @description:获取空间域名
     * @author wuyanwen(2018年3月13日)
     * @param unknown $bucket (bucket名称)
     */
    public function getDomainListOfBucket(string $bucket)
    {
        $uri = sprintf($this->getApiUri('get_bucket_domain', 'api'), $bucket);
        
        return $this->send( $uri, self::GET_METHOD );
    }
    
    /**
     * @description:设置空间权限
     * @author wuyanwen(2018年3月13日)
     * @param string $bucket (bucket名称)
     * @param int $private (0 公开  1 私有)
     */
    public function setPrivate(string $bucket, int $private = 0)
    {
        if (!in_array($private, [0, 1])) return false;
        
        $uri = sprintf('%s?%s', $this->getApiUri('set_bucket_private', 'uc'), http_build_query(['bucket' => $bucket, 'private' => $private]));
        
        return $this->send( $uri, self::POST_METHOD );
    }
    
    /**
     * @description:统计
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月13日
     * @param string $type
     * @param string $begin
     * @param string $end
     * 资源统计
     * @space  获取标准存储的存储量统计     
     * @count  获取标准存储的文件数量统计 
     * @space_line 获取低频存储的存储量统计
     * @count_line 获取低频存储的文件数量统计
     * @blob_transfer 获取跨区域同步流量统计
     * @rs_chtype 获取存储类型请求次数统计
     * @blob_io 获取外网流出流量统计和 GET 请求次数统计
     * @rs_put 获取 PUT 请求次数统计
     * 
     */
    public function statistics(string $begin, string $end, $type = 'space')
    {
        $uri = sprintf($this->getApiUri($type, 'api'), $begin, $end);
        
        return $this->send( $uri, self::GET_METHOD );
    }
}