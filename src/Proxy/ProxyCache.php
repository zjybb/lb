<?php

namespace Zjybb\Lb\Proxy;

use Illuminate\Support\Facades\Cache;

trait ProxyCache
{
    private array $proxy = [];

    private $lockKey = 'lock_';

    public function proxyMethod(string $method, string $cacheKey, int $expire): self
    {
        $this->proxy[$method] = [$cacheKey, $expire];

        return $this;
    }

    public function handleCache($method, $args)
    {
        if (!isset($this->proxy[$method]) || !config('lb.cache_enable')) {
            return $this->getServiceData($method, $args);
        }

        $this->lockKey .= $method;
        [$key, $expire] = $this->proxy[$method];

        $key = sprintf($key, ...array_map(fn($v) => is_array($v) ? base64_encode(gzcompress(serialize($v))) : $v, $args));

        $data = $this->getCacheData($key);
        if (!blank($data)) {
            return $data;
        }

        if ($this->getLock()) {
            return $this->getCacheData($key);
        }

        return $this->makeCache($key, $expire, $method, $args);
    }

    private function getServiceData($method, $args)
    {
        return call_user_func_array([$this->service, $method], $args);
    }

    private function makeCache(string $key, int $expire, string $method, $args)
    {
        $this->makeLock();

        $cache = $this->getServiceData($method, $args);

        Cache::put($key, $cache, now()->addMinutes($expire)->addSeconds(random_int(1, 59)));

        $this->makeLock(true);

        return $cache;
    }

    private function getCacheData($key)
    {
        return Cache::get($key);
    }

    private function getLock(): bool
    {
        $isLock = false;
        while (Cache::has($this->lockKey)) {
            $isLock = true;
            usleep(50000);
        }

        return $isLock;
    }

    private function makeLock($del = false)
    {
        $del ?
            Cache::forget($this->lockKey) :
            Cache::put($this->lockKey, config('lb.lockKey'), now()->addSeconds(10));
    }
}