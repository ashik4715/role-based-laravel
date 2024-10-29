<?php

namespace App\Repositories\ApplicationLog;

use App\Models\Application;
use App\Models\ApplicationLog;
use App\Repositories\EloquentRepositoryInterface;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\DTO\BdMobile;

interface ApplicationLogRepositoryInterface extends EloquentRepositoryInterface
{
    public function getLatestApplicationById(int $id): ?ApplicationLog;
}
