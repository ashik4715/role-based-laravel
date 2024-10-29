<?php

namespace App\Repositories\Application;

use App\Models\Application;
use App\Repositories\EloquentRepositoryInterface;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\DTO\BdMobile;

interface ApplicationRepositoryInterface extends EloquentRepositoryInterface
{
    public function getLatestApplicationByMobile(BdMobile $mobile): ?Application;

    public function storeResubmissionRequest(Application $application, ApplicationData $applicationData);
}
