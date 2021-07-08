<?php

namespace Zjybb\Lb\Log\Formatter;

class JsonFormatter extends \Monolog\Formatter\JsonFormatter
{
    public function format(array $record): string
    {
        $normalized = $this->normalize($record);

        if (isset($normalized['context']) && $normalized['context'] !== []) {
            $normalized = array_merge($normalized, $normalized['context']);
        }
        unset($normalized['context']);

        if (isset($normalized['extra']) && $normalized['extra'] !== []) {
            $normalized = array_merge($normalized, $normalized['extra']);
        }

        unset($normalized['extra']);
        unset($normalized['level']);
        unset($normalized['level_name']);

        return $this->toJson($normalized, true) . ($this->appendNewline ? "\n" : '');
    }
}