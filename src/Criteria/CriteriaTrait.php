<?php

namespace Zjybb\Lb\Criteria;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

trait CriteriaTrait
{
    public function handleEq(array $name, $model)
    {
        foreach ($name as $n) {
            $val = Request::input($n);
            if (!blank($val)) {
                $model = $model->where($n, '=', $val);
            }
        }
        return $model;
    }

    public function handleLike(array $name, $model)
    {
        foreach ($name as $n) {
            $val = Request::input($n);
            if (!blank($val)) {
                if ($model instanceof Collection) {
                    $model = $model->filter(function ($v) use ($n, $val) {
                        return Str::contains($v[$n], $val);
                    });
                } else {
                    $model = $model->where($n, "like", "%{$val}%");
                }
            }
        }
        return $model;
    }

    private function handleTime(array $name, $model)
    {
        foreach ($name as $requestName => $field) {
            $val = Request::input($requestName);
            if (!isset($val['start']) || blank($val['start'])) {
                continue;
            }
            if (!isset($val['end']) || blank($val['end'])) {
                continue;
            }

            $start = Carbon::createFromTimestamp(strtotime($val['start']));
            $end = Carbon::createFromTimestamp(strtotime($val['end']));

            if (in_array($field, ['created_at', 'updated_at'])) {
                $start = $start->toDateTimeString();
                $end = $end->toDateTimeString();
            } else {
                $start = $start->timestamp;
                $end = $end->timestamp;
            }

            $model = $model->whereBetween($field, [$start, $end]);
        }
        return $model;
    }

    public function setDefault($params = [])
    {
        if (!blank($params)) {
            foreach ($params as $k => $v) {
                if (blank(Request::input($k))) {
                    Request::offsetSet($k, $v);
                }
            }
        }
    }

}
