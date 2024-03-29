<?php

namespace Zjybb\Lb\Criteria;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;

trait ServiceTrait
{
    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function findById($id, array $columns = ['*'])
    {
        return $this->findByFilter('id', $id, $columns);
    }

    /**
     * @param string $attribute
     * @param string|int $value
     * @param array $columns
     * @param string $operator
     * @return mixed
     */
    public function findByFilter(string $attribute, $value, array $columns = ['*'], string $operator = '=')
    {
        $this->applyCriteria();
        return tap($this->model->where($attribute, $operator, $value)->first($columns), function () {
            $this->initModel();
        });
    }

    /**
     * @param array $data
     * @param int $id
     * @return mixed
     */
    public function save(array $data, int $id = 0)
    {
        if ($id != 0) {
            $model = $this->findById($id);
            if (blank($model)) {
                return false;
            }
            $model->fill($data);
        } else {
            $model = new $this->model($data);
        }

        if ($model->save()) {
            return $model;
        }
        return false;
    }

    /**
     * @param array $data
     * @param string|int $value
     * @param string $attribute
     * @param string $operator
     * @return mixed
     */
    public function update(array $data, $value, string $attribute = "id", string $operator = '=')
    {
        return $this->model->where($attribute, $operator, $value)->update($data);
    }

    /**
     * @param $ids
     * @return mixed
     */
    public function destroy($ids)
    {
        return $this->model->destroy($ids);
    }

    /**
     * @param string[] $columns
     * @param string[] $order
     * @param null $perPage
     * @return \Illuminate\Support\HigherOrderTapProxy|mixed
     */
    public function paginate(array $columns = ['*'], array $order = ['id' => 'desc'], $perPage = null)
    {
        $perPage = is_null($perPage) ? Request::input('_p', config('lb.perPage', 20)) : $perPage;

        $this->applyCriteria();

        $_o = request()->input('_o', '');
        if (filled($_o) && is_string($_o)) {
            $oArr = explode(',', $_o);
            if (filled($oArr)){
                $m = app($this->model());
                $column = Schema::connection($m->getConnectionName())->getColumnListing($m->getTable());
                foreach ($oArr as $o) {
                    $oData = explode('|', urldecode($o));
                    if (isset($oData[0]) && isset($oData[1])) {
                        if (in_array($oData[1], ['desc', 'asc']) && in_array($oData[0], $column)) {
                            $model = $this->model->orderBy($oData[0], $oData[1]);
                        }
                    }
                }
            }
        }

        foreach ($order as $k => $v) {
            $model = $this->model->orderBy($k, $v);
        }


        return tap($model->paginate($perPage, $columns), function () {
            $this->initModel();
        });
    }

}
