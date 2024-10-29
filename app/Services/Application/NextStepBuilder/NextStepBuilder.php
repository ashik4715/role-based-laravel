<?php

namespace App\Services\Application\NextStepBuilder;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Step\Step;

interface NextStepBuilder
{
    public function getNextStep(ApplicationData $applicationData, string $sectionSlug, string $pageSlug = null): Step;
}
