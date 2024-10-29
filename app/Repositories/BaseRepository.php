<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository implements EloquentRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function update($data)
    {
        return $this->model->update($data);
    }

    public function delete()
    {
        return $this->model->delete();
    }
}
