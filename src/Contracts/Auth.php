<?php

namespace Lizyu\Icloud\Contracts;

interface Auth
{
    public function authorization(...$params);
    
    public function uploadToken(...$params);
    
    public function dowmloadToken(...$params);
}