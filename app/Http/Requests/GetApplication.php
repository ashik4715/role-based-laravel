<?php

namespace App\Http\Requests;

use App\Models\Application;
use App\Repositories\Application\ApplicationRepository;
use App\Repositories\Application\ApplicationRepositoryInterface;
use App\Services\Application\DTO\BdMobile;

trait GetApplication
{
    /**
     * @throws \Exception
     */
    public function getApplication(): Application
    {
        /** @var ApplicationRepository $applicationRepository */
        $applicationRepository = app(ApplicationRepositoryInterface::class);
        $mobile = new BdMobile($this->mobile);

        return $applicationRepository->getLatestApplicationByMobile($mobile);
    }
}
