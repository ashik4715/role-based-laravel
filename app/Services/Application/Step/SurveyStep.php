<?php

namespace App\Services\Application\Step;

use App\Services\Application\Form\Condition;
use App\Services\Application\Form\Fields\Field;
use App\Services\Application\Form\Fields\FieldType;
use App\Services\Application\Form\Fields\FieldTypeException;
use App\Services\Application\ResubmissionRequest\ResubmissionRequestItem;

class SurveyStep extends Step
{
    use SimpleApplicationStepAdder;

    public function __construct(
        ?string $label,
        ?string $description,
        string $sectionSlug,
        int $sectionOrder,
        public readonly Field $field,
        public ?bool $isReSubmissionRequested = null,
        bool $isResubmitted = false,
        bool $showReview = false
    ) {
        parent::__construct(
            type: StepType::SURVEY,
            label: $label,
            description: $description,
            sectionSlug: $sectionSlug,
            sectionOrder: $sectionOrder,
            isReSubmissionRequested: $isReSubmissionRequested,
            isResubmitted: $isResubmitted,
            showReview: $showReview
        );
    }

    /**
     * @throws FieldTypeException
     */
    public static function fromArray(array $array): static
    {
        return new static(
            label: $array['label'],
            description: $array['description'],
            sectionSlug: $array['section_slug'],
            sectionOrder: $array['section_order'],
            field: FieldType::tryFrom($array['field']['type'])->getFieldByArray($array['field']),
            isReSubmissionRequested: $array['is_resubmission_requested'],
            isResubmitted: $array['is_resubmitted']
        );
    }

    public function toArray(): array
    {
        $constantArray = parent::toArray();
        $constantArray['field'] = $this->field->toArray();

        return $constantArray;
    }

    public function requestForResubmission(ResubmissionRequestItem $item)
    {
        $this->isReSubmissionRequested = true;
        $this->field->requestForResubmission($item);
    }

    public function matchConditionValue(Condition $condition)
    {
        return $this->field->matchConditionValue($condition);
    }
}
