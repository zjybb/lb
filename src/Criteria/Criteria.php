<?php

namespace Zjybb\Lb\Criteria;

use Illuminate\Database\Eloquent\Model;

abstract class Criteria
{
    /**
     * @param Model $model
     * @return mixed
     */
    public abstract function apply(Model $model);
}
