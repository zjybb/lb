<?php

namespace Zjybb\Lb\Log\Formatter;

class BaseFormatter
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(new BaseProcessor());
        }
    }
}