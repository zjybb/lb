<?php

namespace Zjybb\Lb\Log\Formatter;

use Monolog\Processor\ProcessorInterface;

class BaseProcessor implements ProcessorInterface
{

    public function __invoke(array $record): array
    {
        $record['context']['app_name'] = config('app.name');
        $record['context']['unique_id'] = get_request_id();
        return $record;
    }
}