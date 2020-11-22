<?php

namespace Zjybb\Lb\Log\Formatter;

class RequestFormatter
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(new BaseProcessor());
            $handler->pushProcessor(function ($record) {
                $record['context']['method'] = request()->getMethod();
                $record['context']['content-type'] = request()->header('Content-Type');
                $record['context']['uri'] = request()->getPathInfo();
                $record['context']['ip'] = request()->getClientIp();
                return $record;
            });
        }
    }
}