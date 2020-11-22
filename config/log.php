<?php


return [
    'default' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'tap' => [\Zjybb\Lb\Log\Formatter\BaseFormatter::class],
        'permission' => 0644,
        'days' => 14,
    ],
    'request' => [
        'driver' => 'daily',
        'path' => storage_path('logs/request.log'),
        'level' => env('LOG_LEVEL', 'info'),
        'tap' => [\Zjybb\Lb\Log\Formatter\RequestFormatter::class],
        'permission' => 0644,
        'days' => 14,
    ],
    'sql' => [
        'driver' => 'daily',
        'path' => storage_path('logs/sql.log'),
        'level' => env('LOG_LEVEL', 'info'),
        'tap' => [\Zjybb\Lb\Log\Formatter\BaseFormatter::class],
        'permission' => 0644,
        'days' => 7,
    ],
    'verify' => [
        'driver' => 'daily',
        'path' => storage_path('logs/verify.log'),
        'level' => env('LOG_LEVEL', 'info'),
        'tap' => [\Zjybb\Lb\Log\Formatter\BaseFormatter::class],
        'permission' => 0644,
        'days' => 14,
    ],
    'info' => [
        'driver' => 'daily',
        'path' => storage_path('logs/info.log'),
        'level' => env('LOG_LEVEL', 'info'),
        'tap' => [\Zjybb\Lb\Log\Formatter\BaseFormatter::class],
        'permission' => 0644,
        'days' => 14,
    ],
    'api' => [
        'driver' => 'daily',
        'path' => storage_path('logs/api.log'),
        'level' => env('LOG_LEVEL', 'info'),
        'tap' => [\Zjybb\Lb\Log\Formatter\BaseFormatter::class],
        'permission' => 0644,
        'days' => 14,
    ],
];