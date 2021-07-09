<?php

namespace Zjybb\Lb\Log\Formatter;

class JsonFormatter extends \Monolog\Formatter\JsonFormatter
{
    protected $topic;

    public function __construct(string $topic = 'default')
    {
        $this->topic = $topic;
        parent::__construct();
    }

    public function format(array $record): string
    {
        $normalized = $this->normalize($record);

        if (isset($normalized['context']) && $normalized['context'] !== []) {
            $context = $normalized['context'];
            unset($normalized['context']);
            $normalized = array_merge($normalized, $context);
        }

        if (isset($normalized['extra']) && $normalized['extra'] !== []) {
            dd(1);
            $extra = $normalized['extra'];
            unset($normalized['extra']);
            $normalized = array_merge($normalized, $extra);
        } else {
            unset($normalized['extra']);
        }

        unset($normalized['level']);
        unset($normalized['level_name']);
        unset($normalized['channel']);

        $normalized['topic'] = $this->topic;

        return $this->toJson($normalized, true) . ($this->appendNewline ? "\n" : '');
    }
}