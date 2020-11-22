<?php

namespace Zjybb\Lb\Sign;

interface SignInterface
{
    public function reset(): SignInterface;

    public function init(string $secret, array $data = []): SignInterface;

    public function sign(): SignInterface;

    public function getSign(): string;

    public function getOriginal(): string;

    public function setOriginal(): SignInterface;
}