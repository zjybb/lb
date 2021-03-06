<?php


return [
    'default' => [
        'driver' => 'daily',
        'path' => storage_path('logs/default.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'tap' => [\Zjybb\Lb\Log\Formatter\BaseFormatter::class],
        'formatter' => \Zjybb\Lb\Log\Formatter\JsonFormatter::class,
        'permission' => 0644,
        'days' => 7,
    ],
    'request' => [
        'driver' => 'daily',
        'path' => storage_path('logs/request.log'),
        'level' => env('LOG_LEVEL', 'info'),
        'tap' => [\Zjybb\Lb\Log\Formatter\RequestFormatter::class],
        'formatter' => \Zjybb\Lb\Log\Formatter\JsonFormatter::class,
        'formatter_with' => ['topic' => 'request'],
        'permission' => 0644,
        'days' => 7,
    ],
    'sql' => [
        'driver' => 'daily',
        'path' => storage_path('logs/sql.log'),
        'level' => env('LOG_LEVEL', 'info'),
        'tap' => [\Zjybb\Lb\Log\Formatter\BaseFormatter::class],
        'formatter' => \Zjybb\Lb\Log\Formatter\JsonFormatter::class,
        'formatter_with' => ['topic' => 'sql'],
        'permission' => 0644,
        'days' => 7,
    ],
    'verify' => [
        'driver' => 'daily',
        'path' => storage_path('logs/verify.log'),
        'level' => env('LOG_LEVEL', 'info'),
        'tap' => [\Zjybb\Lb\Log\Formatter\BaseFormatter::class],
        'formatter' => \Zjybb\Lb\Log\Formatter\JsonFormatter::class,
        'formatter_with' => ['topic' => 'verify'],
        'permission' => 0644,
        'days' => 7,
    ],
    'info' => [
        'driver' => 'daily',
        'path' => storage_path('logs/info.log'),
        'level' => env('LOG_LEVEL', 'info'),
        'tap' => [\Zjybb\Lb\Log\Formatter\BaseFormatter::class],
        'formatter' => \Zjybb\Lb\Log\Formatter\JsonFormatter::class,
        'formatter_with' => ['topic' => 'info'],
        'permission' => 0644,
        'days' => 7,
    ],
    'api' => [
        'driver' => 'daily',
        'path' => storage_path('logs/api.log'),
        'level' => env('LOG_LEVEL', 'info'),
        'tap' => [\Zjybb\Lb\Log\Formatter\BaseFormatter::class],
        'formatter' => \Zjybb\Lb\Log\Formatter\JsonFormatter::class,
        'formatter_with' => ['topic' => 'api'],
        'permission' => 0644,
        'days' => 7,
    ],
];