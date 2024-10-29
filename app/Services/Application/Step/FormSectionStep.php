<?php

namespace App\Services\Application\Step;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\ApplicationData\ApplicationDataFormSection;
use App\Services\Application\Exceptions\FieldNotFoundException;
use App\Services\Application\Form\Condition;
use App\Services\Application\Form\Fields\Field;
use App\Services\Application\Form\Fields\FieldType;
use App\Services\Application\Form\Fields\FieldTypeException;
use App\Services\Application\Form\Fields\GroupField;
use App\Services\Application\ResubmissionRequest\ResubmissionRequestItem;
use App\Services\Application\Status;

class FormSectionStep extends Step
{
    public function __construct(
        ?string                $label,
        ?string                $description,
        string                 $sectionSlug,
        int                    $sectionOrder,
        public readonly string $pageSlug,
        private array          $fields = [],
        public readonly ?int   $pageOrder = null,
        bool                   $isResubmitted = false,
        ?bool                  $isReSubmissionRequested = null,

        bool                   $showReview = false
    )
    {
        parent::__construct
        (
            type: StepType::FORM_SECTION,
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
        $fields = [];
        foreach ($array['values'] as $value) {
            $fields[$value['slug']] = FieldType::tryFrom($value['type'])->getFieldByArray($value);
        }

        return new static(
            label: $array['label'],
            description: $array['description'],
            sectionSlug: $array['section_slug'],
            sectionOrder: $array['section_order'],
            pageSlug: $array['page_slug'],
            fields: $fields,
            pageOrder: $array['page_order'],
            isResubmitted: $array['is_resubmitted'],
            isReSubmissionRequested: $array['is_resubmission_requested'],
            showReview: $array['show_review'] ?? false
        );
    }

    public function toArrayForReview(Status $status): array
    {
        $isResubmitRequested = $this->isReSubmissionRequested;

        if ($status == Status::RESUBMISSION_REQUESTED) {
            $isResubmitRequested = ($this->isReSubmissionRequested !== null) ? $this->isReSubmissionRequested : false;
        } elseif ($this->isReSubmissionRequested === null) {
            $isResubmitRequested = null;
        }
        if ($status == Status::SUBMITTED || $status == Status::APPROVED || $status == Status::REJECTED || $status == Status::RESUBMITTED) $isResubmitRequested = false;

        $data = parent::toArray();
        $data['page_slug'] = $this->pageSlug;
        $data['page_order'] = $this->pageOrder;
        $data['is_resubmission_requested'] = $isResubmitRequested;
        $data['is_resubmitted'] = $this->isResubmitted;
        foreach ($this->getFields() as $field) {
            $data['fields'][] = $field->toArrayForReview($status);
        }

        return $data;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['page_slug'] = $this->pageSlug;
        $data['page_order'] = $this->pageOrder;
        $data['is_resubmission_requested'] = $this->isReSubmissionRequested;
        $data['is_resubmitted'] = $this->isResubmitted;
        $data['values'] = array_map(function (Field $field) {
            return $field->toArray();
        }, $this->getFields());
        return $data;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        uasort($this->fields, function ($a, $b) {
            return $a->order > $b->order;
        });
        return $this->fields;
    }

    public function toArrayForAdmin(): array
    {
        $data = parent::toArray();
        $data['page_slug'] = $this->pageSlug;
        $data['page_order'] = $this->pageOrder;
        $data['is_resubmission_requested'] = $this->isReSubmissionRequested;
        $data['is_resubmitted'] = $this->isResubmitted;
        foreach ($this->getFields() as $key => $field) {
            $data['fields'][$key] = $field->toArrayForApi();
        }

        return $data;
    }

    public function toArrayForApi(): array
    {
        $data = parent::toArray();
        $data['page_slug'] = $this->pageSlug;
        $data['page_order'] = $this->pageOrder;
        $data['is_resubmission_requested'] = $this->isReSubmissionRequested;
        $data['is_resubmitted'] = $this->isResubmitted;
        foreach ($this->getFields() as $field) {
            $data['fields'][] = $field->toArrayForApi();
        }

        return $data;
    }

    public function addOrUpdateField(Field $field): self
    {
        $this->fields[$field->slug] = $field;

        return $this;
    }

    /**
     * @throws FieldNotFoundException
     */
    public function requestForResubmission(ResubmissionRequestItem $item)
    {
        $field = $this->getField($item->fieldSlug);
        if (is_null($field)) {
            throw new FieldNotFoundException();
        }
        $visibilityDependentFields = $field->visibilityDependentField ?? null;
        $addressDependentField = $field->dependentField ?? null;
        if ($visibilityDependentFields) {
            foreach ($visibilityDependentFields as $visibilityDependentField) {
                /** @var Field $field */
                $dependentField = $this->getField($visibilityDependentField);

                if (is_null($dependentField)) {
                    throw new FieldNotFoundException();
                }
                if ($dependentField instanceof GroupField) {
                    foreach ($dependentField->getChildren() as $child) {
                        $child->requestForResubmission($item);
                    }
                } else {
                    $dependentField->requestForResubmission($item);
                }
            }
        } elseif ($addressDependentField) {
            while ($addressDependentField != null) {
                $addressField = $this->getField($addressDependentField);
                if (is_null($addressField)) {
                    throw new FieldNotFoundException();
                }
                $addressField->requestForResubmission($item);
                $addressDependentField = $addressField->dependentField;
            }
        }
        $this->isReSubmissionRequested = true;
        $field->requestForResubmission($item);
    }

    public function getField(string $slug): ?Field
    {
        $field = $this->fields[$slug] ?? null;
        if ($field) {
            return $field;
        }

        foreach ($this->fields as $field) {
            if ($field->type === FieldType::GROUP && $field->hasChild($slug)) {
                return $field->getChild($slug);
            }
        }

        return null;
    }

    public function addStepToApplicationData(ApplicationData $applicationData)
    {
        $section = $applicationData->getSection($this->sectionSlug) ?? (new ApplicationDataFormSection());
        $section->addOrUpdatePage($this);
        $applicationData->addOrUpdateSection($section);
    }

    public function matchConditionValue(Condition $condition)
    {
        return $this->getField($condition->field)->matchConditionValue($condition);
    }
}
