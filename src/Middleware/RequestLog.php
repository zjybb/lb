<?php

namespace Zjybb\Lb\Middleware;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestLog
{
    public function handle(Request $request, Closure $next)
    {
        set_request_id();
        return $next($request);
    }

    public function terminate(Request $request, $response)
    {
        $this->handleRequestLog($response);
        $this->handleSqlLog();
    }

    private function handleRequestLog($response)
    {
        if (config('lb.request_log.enable', true)) {

            $path = request()->getPathInfo();
            if ($path == '/' || Str::startsWith($path, config('lb.request_log.filter', []))) {
                return;
            }

            if (request()->isMethod('OPTIONS')) {
                return;
            }

            $http_code = 0;
            $content   = [];
            if ($response instanceof Response) {
                $content   = json_decode($response->getContent(), 1);
                $http_code = $response->getStatusCode();
            }

            $context = [
                'duration'  => round((microtime(true) - request()->server('REQUEST_TIME_FLOAT')) * 1000, 2),
                'http_code' => $http_code,
                'request'   => request()->input(),
                'response'  => $content,
                'resp_code' => Arr::get($content, 'code', ''),
                'resp_msg'  => Arr::get($content, 'errMsg', ''),
            ];

            log_info('', $context, 'request', false);
        }

    }

    private function handleSqlLog()
    {
        if (config('lb.sql_log.enable', true)) {

            $sqlList = request()->server->get('sql_log');
            if (!blank($sqlList)) {
                log_info('', $sqlList, 'sql', false);
            }

        }
    }

}
