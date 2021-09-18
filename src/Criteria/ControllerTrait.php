<?php

namespace Zjybb\Lb\Criteria;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Zjybb\Response\Resp;

trait ControllerTrait
{
    /**
     * @var JsonResource
     */
    public JsonResource $resource;

    public bool $recordUser = true;

    abstract protected function _resource(): string;

    abstract protected function _request(): string;

    abstract protected function _service();

    /**
     * 列表
     * @return mixed
     */
    public function index()
    {
        if (method_exists($this, 'indexBeforeEvent')) {
            $this->indexBeforeEvent();
        }

        $list = $this->_service()->paginate();

        if (blank($list)) {
            return Resp::success();
        }

        if (Str::endsWith($this->_resource(), 'Resource')) {
            return Resp::resource($this->_resource()::collection($list));
        }

        return Resp::resource($list);
    }

    /**
     * 详情
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        if (method_exists($this, 'showBeforeEvent')) {
            $this->showBeforeEvent();
        }

        $info = $this->_service()->findById($id);

        if (blank($info)) {
            return Resp::success();
        }

        if ($this->isResource()) {
            $resourceObj = $this->_resource();
            return Resp::resource(new $resourceObj($info));
        }

        return Resp::resource($info);
    }

    /**
     * 创建
     * @return mixed
     */
    public function store()
    {
        $request = app($this->_request());
        $request->validated();

        $data = $this->getData('createData');
        $res = $this->_service()->save($data);

        if (!$res) {
            return Resp::msg(trans('lb::msg.create_fail'));
        }

        return Resp::success();
    }

    /**
     * 修改
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $request = app($this->_request());
        $request->validated();

        $data = $this->getData('editData');
        $res = $this->_service()->save($data, $id);

        if (!$res) {
            return Resp::msg(trans('lb::msg.edit_fail'));
        }

        return Resp::success();
    }

    /**
     * 单项删除
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $res = $this->_service()->destroy($id);

        if ($res) {
            return Resp::success();
        }

        return Resp::msg(trans('lb::msg.delete_fail'));
    }

    /**
     * 多项删除
     * @return mixed
     */
    public function delete()
    {
        $ids = request()->input('id');

        $res = $this->_service()->destroy($ids);

        if ($res) {
            return Resp::success();
        }

        return Resp::msg(trans('lb::msg.delete_fail'));
    }

    /**
     * 入库数据
     * @param string $name
     * @return mixed
     */
    private function getData(string $name)
    {
        return method_exists($this, $name) ? $this->$name() : app($this->_request())->all();
    }

    /**
     * 自定义创建数据
     * @return array
     */
    public function createData(): array
    {
        $data = [];

        if ($this->recordUser) {
            $data[$this->createUserField()] = Auth::user()->id ?? 0;
            $data[$this->editUserField()] = Auth::user()->id ?? 0;
        }

        return array_merge($data, app($this->_request())->all());
    }

    /**
     * 自定义修改数据
     * @return array
     */
    public function editData(): array
    {
        $data = [];

        if ($this->recordUser) {
            $data[$this->editUserField()] = Auth::user()->id ?? 0;
        }

        return array_merge($data, app($this->_request())->all());
    }

    /**
     * 判断是否返回资源
     * @param $name
     * @return bool
     */
    private function isResource($name = null): bool
    {
        $name = is_null($name) ? $this->_resource() : $name;
        return Str::endsWith($name, 'Resource');
    }

    /**
     * @return string
     */
    public function createUserField(): string
    {
        return 'create_user';
    }

    /**
     * @return string
     */
    public function editUserField(): string
    {
        return 'update_user';
    }
}
