<?php

namespace Zjybb\Lb\Criteria;

use Illuminate\Support\Facades\Request;

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
        return tap($this->model->where($attribute, $operator, $value)->first($columns)->toArray(), function () {
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
        if ($id !== 0) {
            $model = $this->findById($id);
            if (blank($model)) {
                return false;
            }
        } else {
            $model = $this->model;
        }

        $fillable = $this->model->getFillable();

        foreach ($data as $field => $value) {
            if (!blank($value)) {
                if (!blank($fillable) && !in_array($field, $fillable)) {
                    continue;
                }
                $model->setAttribute($field, $value);
            }
        }

        return $model->save($data);
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
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function batchDelete(array $ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
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

        foreach ($order as $k => $v) {
            $model = $this->model->orderBy($k, $v);
        }

        return tap($model->paginate($perPage, $columns), function () {
            $this->initModel();
        });
    }

}
