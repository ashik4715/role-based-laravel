<?php

namespace App\Services\Application\ApplicationData;

use App\Services\Application\Form\Condition;
use App\Services\Application\ResubmissionRequest\ResubmissionRequestItem;
use App\Services\Application\Status;
use App\Services\Application\Step\Step;
use App\Services\Application\Step\StepType;

class ApplicationDataGenericSection extends ApplicationDataSection
{
    public function __construct(public readonly Step $step, ?bool $isReSubmissionRequested = null, bool $isResubmitted = false)
    {
        parent::__construct($isReSubmissionRequested, $isResubmitted);
    }

    public static function fromArray(array $array): static
    {
        if (! isset($array['type'])) {
        }
        return new static(
            step: StepType::tryFrom($array['type'])->getStepByArray($array),
            isReSubmissionRequested: $array['is_resubmission_requested'],
            isResubmitted:  $array['is_resubmitted'],
        );
    }

    public function getSectionSlug(): string
    {
        return $this->step->sectionSlug;
    }

    public function toArrayForApi(): array
    {
        return $this->step->toArray();
    }

    public function toArrayForReview(Status $status): array
    {
        return $this->step->toArrayForReview($status);
    }

    public function toArrayForAdmin(): array
    {
        return $this->step->toArray();
    }

    public function toArray(): array
    {
        return $this->step->toArray();
    }

    public function requestForResubmission(ResubmissionRequestItem $item)
    {
        $this->isReSubmissionRequested = true;
        $this->step->requestForResubmission($item);
    }

    public function matchConditionValue(Condition $condition)
    {
        return $this->step->matchConditionValue($condition);
    }

    public function getSectionOrder(): int
    {
        return $this->step->sectionOrder;
    }

    public function isReSubmissionRequested(): ?bool
    {
        return $this->step->isReSubmissionRequested;
    }

    public function getFirstPage(): Step
    {
        return $this->step;
    }

    public function getStep(?string $pageSlug): ?Step
    {
        return $this->step;
    }

    public function getPages()
    {
        return [$this->step];
    }
}
