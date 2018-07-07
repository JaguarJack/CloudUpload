<?php

return [
    
    /* 云上传驱动 */
    'dirver' => 'qiniu',
    
    /* 七牛配置信息 */
    'qiniu'  => [
        'qiNiuKey'    => '',
        'qiNiuSecret' => '',
        
        //上传策略字段，上传凭证校验使用
        'policyFields' => [
            'callbackUrl',
            'callbackBody',
            'callbackHost',
            'callbackBodyType',
            'callbackFetchKey',
            'returnUrl',
            'returnBody',
            'endUser',
            'saveKey',
            'insertOnly',
            'detectMime',
            'mimeLimit',
            'fsizeMin',
            'fsizeLimit',
            'persistentOps',
            'persistentNotifyUrl',
            'persistentPipeline',
            'deleteAfterDays',
            'fileType',
            'isPrefixalScope',
        ],
    ],
    
    /* 又拍云配置信息 */
    'upyun'  =>  [
        'opreator'  => '',
        'password'  => '',
        
        'buckets'   => [''],
    ],
    
    //api接口
    'host' => [
        //七牛host
        'rs'    => 'rs.qiniu.com',
        'api'   => 'api.qiniu.com',
        'uc'    => 'uc.qbox.me',
        'rsf'   => 'rsf.qbox.me',
        'iovip' => 'iovip.qbox.me',
        'up'    => 'up.qiniu.com',
        //又拍host
        'v0'    => 'v0.api.upyun.com',
        'v1'    => 'v1.api.upyun.com',
        'v2'    => 'v2.api.upyun.com',
        'v3'    => 'v3.api.upyun.com',
    ],
    
    'api'      => [
        //七牛buckets uri
        'buckets'            => '/buckets',
        'create_bucket'      => '/mkbucketv2/%s/region/%s',
        'drop_bucket'        => '/drop/%s',
        'get_bucket_domain'  => '/v6/domain/list?tbl=%s',
        'set_bucket_private' => '/private',
        //七牛数据统计uri
        'space'              => '/v6/space?begin=%s&end=%s&g=day',
        'count'              => '/v6/count?begin=%s&end=%s&g=day',
        'space_line'         => '/v6/space_line?begin=%s&end=%s&g=day',
        'count_line'         => '/v6/count_line?begin=%s&end=%s&g=day',
        'blob_transfer'      => '/v6/blob_transfer?begin=%s&end=%s&g=day&select=size',
        'rs_chtype'          => '/v6/rs_chtype?begin=%s&end=%s&g=day&select=hits',
        'blob_io'            => '/v6/blob_io?begin=%s&end=%s&g=day&select=flow&$src=origin',
        'rs_put'             => '/v6/rs_put?begin=%s&end=%s&g=day&select=hits',
        //七牛object Uri
        'stat'              => '/stat/%s',
        'chgm'              => '/chgm/%s/mime/%s/x-qn-meta-%s/%s/cond/%s',
        'move'              => '/move/%s/%s',
        'copy'              => '/copy/%s/%s',
        'delete'            => '/delete/%s',
        'list'              => '/list?bucket=%s&marker=%s&limit=%d&prefix=%s&delimiter=%s',
        'fetch'             => '/fetch/%s/to/%s',
        'batch'             => '/batch',
        'prefetch'          => '/prefetch/%s',
        'deleteAfterDays'   => '/deleteAfterDays/%s/%s',
        'chtype'            => '/chtype/%s/type/%s',
        'upload'            => '/v6/rs_put?begin=%s&end=%s&g=day&select=hits',
        'mkblk'             => '/mkblk/%s',
        'bput'              => '/bput/%s/%s',
        'mkfile'            => '/mkfile/%s/key/%s/mimeType/%s/x:user-var/%s',
        
        //又拍
        'folder'            => '/%s/',
        'drop_dir'          => '/%s/',
        'usage'             => '/%s/?usage',
    ]
    
    
];
