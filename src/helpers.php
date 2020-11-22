<?php

const X_REQUEST_ID = 'X-REQUEST-ID';

if (!function_exists('set_request_id')) {
    function set_request_id()
    {
        request()->server->set('X_REQUEST_ID', \Illuminate\Support\Str::uuid()->getHex()->toString());
    }
}

if (!function_exists('get_request_id')) {
    function get_request_id()
    {
        return request()->server->get('X-REQUEST-ID', '');
    }
}

if (!function_exists('format_duration')) {
    function format_duration($seconds): string
    {
        if ($seconds < 0.001) {
            return round($seconds * 1000000) . 'μs';
        } elseif ($seconds < 1) {
            return round($seconds * 1000, 2) . 'ms';
        }
        return round($seconds, 2) . 's';
    }
}

if (!function_exists('log_info')) {
    function log_info($message, array $context = [], $channel = 'daily', $queue = true)
    {
        log_c('INFO', ...func_get_args());
    }
}

if (!function_exists('log_debug')) {
    function log_debug($message, array $context = [], $channel = 'default', $queue = true)
    {
        log_c('DEBUG', ...func_get_args());
    }
}

if (!function_exists('log_error')) {
    function log_error($message, array $context = [], $channel = 'default', $queue = true)
    {
        log_c('ERROR', ...func_get_args());
    }
}

if (!function_exists('log_c')) {
    function log_c(string $level, $message, array $context = [], $channel = 'default', $queue = true)
    {
        $queue ?
            \Zjybb\Lb\Jobs\LogJob::dispatch($message, $context, $channel, $level, $queue) :
            \Zjybb\Lb\Jobs\LogJob::dispatchSync($message, $context, $channel, $level, $queue);
    }
}

if (!function_exists('verify_ip')) {
    function verify_ip($ip, $ip_str): bool
    {
        $mark_len = 32;
        if (strpos($ip_str, "/") > 0) {
            list($ip_str, $mark_len) = explode("/", $ip_str);
        }
        $right_len = 32 - $mark_len;
        return ip2long($ip) >> $right_len == ip2long($ip_str) >> $right_len;
    }
}

if (!function_exists('random_number')) {
    function random_number($length = 6): string
    {
        $num = '';
        for ($i = 0; $i < $length; $i++) {
            $num .= random_int(0, 9);
        }
        return $num;
    }
}

if (!function_exists('is_login')) {
    function is_super(): bool
    {
        return \Illuminate\Support\Facades\Auth::check();
    }
}

if (!function_exists('is_super')) {
    function is_super(): bool
    {
        return \Illuminate\Support\Facades\Auth::id() == 1;
    }
}
