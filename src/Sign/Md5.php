<?php

namespace Zjybb\Lb\Sign;

class Md5 extends Sign
{
    public function sign(): self
    {
        $this->setOriginal();

        $this->sign = strtolower(md5($this->secret . '=' . $this->original));

        return $this;
    }

}