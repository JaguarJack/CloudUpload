<?php

namespace Lizyu\Icloud\Exceptions;

class NotFoundException extends \Exception
{
    /**
     * @description:键值未找到
     * @author: wuyanwen <wuyanwen1992@gmail.com>
     * @date:2018年3月11日
     * @return \Lizyu\Permission\Exceptions\UnauthorizedException
     */
    public static function NotFoundKey(string $msg)
    {
        return new static($msg, 500, null);
    }
    
    /**
     * @description:方法未找到
     * @author wuyanwen(2018年3月16日)
     * @param string $msg
     * @return \Lizyu\Icloud\Exceptions\NotFoundException
     */
    public static function NotFoundMethod(string $msg)
    {
        return new static($msg, 500, null);
    }
}