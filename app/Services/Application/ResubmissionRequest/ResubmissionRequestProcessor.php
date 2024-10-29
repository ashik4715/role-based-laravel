<?php

namespace App\Services\Application\ResubmissionRequest;

use App\Models\Application;
use App\Repositories\Application\ApplicationRepositoryInterface;
use App\Services\Application\Exceptions\InvalidResubmissionRequestException;
use App\Services\Configuration\ConfigurationService;

class ResubmissionRequestProcessor
{
    public function __construct(private readonly ConfigurationService $configurationService, private readonly ApplicationRepositoryInterface $applicationRepository)
    {
    }

    /**
     * @param  array<ResubmissionRequestItem>  $resubmissionItems
     *
     * @throws InvalidResubmissionRequestException
     */
    public function requestForResubmission(Application $application, array $resubmissionItems): bool
    {
        $this->validateApplicationEligibleForResubmission($application);

        $applicationData = $application->getApplicationData()->requestForResubmission($resubmissionItems);
        $this->applicationRepository->storeResubmissionRequest($application, $applicationData);

        return true;
    }

    /**
     * @throws InvalidResubmissionRequestException
     */
    public function validateApplicationEligibleForResubmission(Application $application): void
    {
        if (! $application->isCurrentStatusEligibleForResubmissionRequest()) {
            throw new InvalidResubmissionRequestException('You can\'t update this application at this moment');
        }
    }
}
