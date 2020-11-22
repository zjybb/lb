<?php

namespace Zjybb\Lb\Sign;

abstract class Sign implements SignInterface
{
    protected array $data;
    protected string $original, $sign, $secret;
    protected $set = false;

    public function reset(): self
    {
        $this->set = false;
        $this->secret = '';
        $this->sign = '';
        $this->original = '';
        $this->data = [];

        return $this;
    }

    public function init(string $secret, array $data = []): self
    {
        $this->reset();
        $this->secret = $secret;
        $this->data = !blank($data) ? $data : \Illuminate\Support\Facades\Request::except('sign');
        ksort($this->data);

        return $this;
    }

    public function getSign(): string
    {
        return $this->sign;
    }

    public function setOriginal(): self
    {
        if ($this->set) {
            return $this;
        }

        foreach ($this->data as $k => $v) {
            $this->original .= "{$k}={$v}&";
        }
        $this->original = substr($this->original, 0, -1);

        return $this;
    }

    public function getOriginal(): string
    {
        return $this->original;
    }

}