<?php

namespace App\Repositories;

use App\Repositories\Contract\BaseInterface;

class BaseRepository implements BaseInterface
{
    /**
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return get_class($this->model);
    }

    public function getTable(): string
    {
        return $this->model->getTable();
    }

    public function get(?array $ids = null)
    {
        if ($ids !== null || is_array($ids)) {
            return $this->model->whereIn('id', $ids)->get();
        }
        return $this->model->get();
    }

    public function paginate($pag = 20)
    {
        return $this->model->paginate($pag);
    }

    /**
     * @return Illuminate\Database\Eloquent\Model
     */
    public function create(array $input)
    {
        return $this->model->create($input);
    }

    /**
     * @return Illuminate\Database\Eloquent\Model
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    public function updateOrCreate($where, $input)
    {
        return $this->model->updateOrCreate($where, $input);
    }

    public function where($where, $value)
    {
        return $this->model->where($where, $value);
    }

    public function update(int $id, array $input)
    {
        if (!$this->find($id)) {
            throw new \Exception('Registro ' . $id . ' não encontrado na tabela "' . $this->getTable() . '"!');
        }

        return $this->find($id)->update($input);
    }

    /**
     * @param integer|array $id
     * @return bool
     */
    public function delete($id)
    {
        if (is_array($id)) {
            return $this->model->whereIn('id', $id)->delete();
        }

        if (!$this->find($id)) {
            throw new \Exception('Registro não encontrado!');
        }

        return $this->find($id)->delete();
    }

    public function newQuery()
    {
        return $this->model;
    }
}
