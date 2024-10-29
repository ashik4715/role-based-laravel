<?php

namespace App\Services\Application\Step;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Form\Condition;

class InformationStep extends StepWithPage
{
    use InvalidResubmissionRequestStep;
    public function __construct(string $label, string $description, string $sectionSlug, int $sectionOrder, string $pageSlug, int $pageOrder)
    {
        parent::__construct(
            label: $label,
            description: $description,
            sectionSlug: $sectionSlug,
            sectionOrder: $sectionOrder,
            type: StepType::INFORMATION,
            pageSlug: $pageSlug,
            pageOrder: $pageOrder
        );
    }

    public function addStepToApplicationData(ApplicationData $applicationData)
    {
        // TODO: Implement addStepToApplicationData() method.
    }

    public function matchConditionValue(Condition $condition)
    {
    }
}
