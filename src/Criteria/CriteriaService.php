<?php

namespace Zjybb\Lb\Criteria;

use Illuminate\Database\Eloquent\Model;

abstract class CriteriaService
{
    use ServiceTrait;

    protected array $criteriaList = [];
    protected bool $skipCriteria = false;

    protected $model;

    abstract protected function model(): string;

    public function __construct()
    {
        $this->initModel();
        $this->resetCriteria();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    private function initModel(): self
    {
        $this->model = app($this->model());

        if (!$this->model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getCriteriaList(): array
    {
        return $this->criteriaList;
    }

    /**
     * @param Criteria $criteria
     * @param bool $header
     * @return $this
     */
    public function pushCriteria(Criteria $criteria, bool $header = false): self
    {
        $header ? array_unshift($this->criteriaList, $criteria) : $this->criteriaList[] = $criteria;
        return $this;
    }

    /**
     * @return $this
     */
    public function resetCriteria(): self
    {
        $this->criteriaList = [];
        return $this;
    }

    /**
     * @param bool $bool
     * @return self
     */
    public function skipCriteria(bool $bool = true): self
    {
        $this->skipCriteria = $bool;
        return $this;
    }

    /**
     * applyCriteria
     */
    public function applyCriteria()
    {
        if ($this->skipCriteria) {
            return;
        }

        foreach ($this->getCriteriaList() as $criteria) {
            if ($criteria instanceof Criteria) {
                $this->model = $criteria->apply($this->model);
            }
        }

    }

}
