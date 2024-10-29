<?php

namespace App\Services\Application\Step;

use App\Helpers\JsonAndArrayAble;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Form\Condition;
use App\Services\Application\ResubmissionRequest\ResubmissionRequestItem;

abstract class Step extends JsonAndArrayAble
{

    public function __construct(
        public readonly StepType $type,
        public readonly ?string $label = null,
        public readonly ?string $description = null,
        public readonly ?string $sectionSlug = null,
        public readonly ?int $sectionOrder = null,
        public ?bool $isReSubmissionRequested = null,
        public bool $isResubmitted = false,
        public bool $showReview = true,
    ) {
    }

    public static function fromArray(array $array): static
    {
        return new static(
            type: StepType::tryFrom($array['type']),
            label: $array['label'],
            description: $array['description'],
            sectionSlug: $array['section_slug'],
            sectionOrder: $array['section_order'],
            value: $array['value'],
            isReSubmissionRequested: $array['is_resubmission_requested'],
            isResubmitted: $array['is_resubmitted']
        );
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'description' => $this->description,
            'section_slug' => $this->sectionSlug,
            'type' => $this->type->value,
            'section_order' => $this->sectionOrder,
            'is_resubmission_requested' => $this->isReSubmissionRequested,
            'is_resubmitted' => $this->isResubmitted,
            'show_review' => $this->showReview,
        ];
    }

    public function toArrayForApi(): array
    {
        return  $this->toArray();
    }

    abstract public function addStepToApplicationData(ApplicationData $applicationData);

    abstract public function requestForResubmission(ResubmissionRequestItem $item);

    abstract public function matchConditionValue(Condition $condition);
}
