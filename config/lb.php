<?php

return [
    //更改默认语言和时区
    'change_local' => true,

    //开启proxy缓存
    'cache_enable' => env('PROXY_CACHE_ENABLE', true),

    //默认分页
    'perPage' => 20,

    //访问日志
    'request_log' => [
        'enable' => env('LB_REQUEST_LOG_ENABLE', true),
        'filter' => [
            '/ping',
            '/aCG7krpwbNvSBnJo/',
            '/b9BTeI0lp2uqW3d4/',
            '/TdJXbaYRl9Qf6Pwv/',
        ]
    ],

    //sql日志
    'sql_log' => [
        'enable' => env('LB_SQL_LOG', true),
        'slow' => 2000,
        'filter' => [
            '/ping',
            '/aCG7krpwbNvSBnJo/',
            '/b9BTeI0lp2uqW3d4/',
            '/TdJXbaYRl9Qf6Pwv/',
        ]
    ],

];