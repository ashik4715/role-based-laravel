<?php

namespace App\Services\Application;

use App\Repositories\Section\SectionRepositoryInterface;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Step\ReviewStep;

class SectionService
{
    public function __construct(private readonly SectionRepositoryInterface $sectionRepository)
    {
    }

    public function getReviewStep(ApplicationData $applicationData): ReviewStep
    {
        $review = $this->sectionRepository->getSectionBySlug('review');
        return $review->getReviewStep($applicationData);
    }
}
