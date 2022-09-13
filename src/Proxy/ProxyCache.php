<?php

namespace Zjybb\Lb\Proxy;

use Illuminate\Support\Facades\Cache;

trait ProxyCache
{
    private array $proxy = [];

    private $lockKey = '';
    private $coolKey = '';
    private $hotKey = '';

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

        $this->lockKey = 'lock_' . $method;
        [$key, $expire] = $this->proxy[$method];

        $this->setKeys(sprintf($key, ...array_map(fn($v) => is_array($v) ? base64_encode(gzcompress(serialize($v))) : $v, $args)));

        $data = $this->getCacheData();
        if (!blank($data)) {
            return $data;
        }

        $lock = Cache::lock($this->lockKey, 10);
        if ($lock->get()) {
            $data = $this->makeCache($expire, $method, $args);
            $lock->release();
            return $data;
        }

        for ($i = 0; $i < 5; $i++) {
            $data = $this->getCoolCacheData();
            if (!blank($data)) {
                return $data;
            }
            usleep(100000);
        }

        log_info("[proxy cache] 缓存为空", ["method" => $method]);

        return null;
    }

    private function getServiceData($method, $args)
    {
        return call_user_func_array([$this->service, $method], $args);
    }

    private function makeCache(int $expire, string $method, $args)
    {
        $data = $this->getServiceData($method, $args);

        if (!blank($data)) {
            Cache::put($this->hotKey, $data, now()->addMinutes($expire)->addSeconds(random_int(1, 59)));
            Cache::forever($this->coolKey, $data);
        }

        return $data;
    }

    private function getCacheData()
    {
        return Cache::get($this->hotKey);
    }

    private function getCoolCacheData()
    {
        return Cache::get($this->coolKey);
    }

    private function setKeys($key)
    {
        $this->hotKey  = '';
        $this->coolKey = '';
        $this->hotKey  .= $key;
        $this->coolKey .= 'cool:' . $key;
    }
}
