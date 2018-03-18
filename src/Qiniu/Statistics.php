<?php
/**
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
namespace Lizyu\Icloud\Qiniu;

use Lizyu\Icloud\IcloudAbstract;

final class Statistics extends IcloudAbstract
{
    /**
     * @description:统计
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月13日
     * @param string $type
     * @param string $begin
     * @param string $end
     */
    public function statistics(string $begin, string $end, $type = 'space')
    {
        $uri = sprintf($this->getApiUri($type, 'api'), $begin, $end);
        
        $response = $this->send( $uri, self::GET_METHOD );
        
        dd($response->getBody()->getContents());
    }
}