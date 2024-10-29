<?php

namespace App\Services\Application\Step;

use App\Services\Application\Form\Condition;

class SuccessStep extends Step
{
    use SimpleApplicationStepAdder, InvalidResubmissionRequestStep;

    public function __construct(?string $label, ?string $description, string $sectionSlug, string $sectionOrder)
    {
        parent::__construct(type: StepType::SUCCESS, label: $label, description: $description, sectionSlug: $sectionSlug, sectionOrder: $sectionOrder);
    }

    public function matchConditionValue(Condition $condition)
    {
        // TODO: Implement matchConditionValue() method.
    }
}
