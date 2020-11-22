<?php

namespace Zjybb\Lb\Proxy;

class ServiceProxy
{
    use ProxyCache;

    private $service;

    public function __construct($service)
    {
        $this->service = $service;
    }

    public function __call($name, $arguments)
    {
        return $this->handleCache($name, $arguments);
    }

}