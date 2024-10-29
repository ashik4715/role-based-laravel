<?php

namespace App\Repositories\ApplicationLog;

use App\Models\ApplicationLog;
use App\Repositories\BaseRepository;

class ApplicationLogRepository extends BaseRepository implements ApplicationLogRepositoryInterface
{
    protected $model;

    public function __construct(ApplicationLog $model)
    {
        $this->model = $model;
        parent::__construct($model);
    }

    public function getLatestApplicationById(int $id): ?ApplicationLog
    {
        return $this->model->where('application_id', $id)->latest()->first();
    }
}
