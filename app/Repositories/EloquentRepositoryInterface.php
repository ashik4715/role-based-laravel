<?php

namespace App\Repositories;

interface EloquentRepositoryInterface
{
    public function create(array $attributes);

    public function find($id);

    public function update($data);

    public function delete();
}
