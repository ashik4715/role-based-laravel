<?php

namespace App\Services\Application\Step;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\ApplicationData\ApplicationDataNidSection;
use App\Services\Application\DTO\NidDTO;
use App\Services\Application\Form\Condition;
use App\Services\Application\ResubmissionRequest\ResubmissionRequestItem;

class NidStep extends StepWithPage
{
    public function __construct(
        ?string $label,
        ?string $description,
        string $sectionSlug,
        int $sectionOrder,
        string $pageSlug,
        int $pageOrder,
        private ?NidDTO $value = null,
        protected ?string $resubmissionNote = null,
        protected ?bool $isResubmissionRequested = null,
        public bool $isResubmitted = false,
        public bool $showReview = false
    ) {
        parent::__construct(
            label: $label,
            description: $description,
            sectionSlug: $sectionSlug,
            sectionOrder: $sectionOrder,
            type: StepType::NID,
            pageSlug: $pageSlug,
            pageOrder: $pageOrder,
            isResubmitted: $isResubmitted,
            isResubmissionRequested: $isResubmissionRequested,
            showReview: $showReview
        );
    }

    public static function fromArray(array $array): static
    {
        $data = new static(
            label: $array['label'],
            description: $array['description'],
            sectionSlug: $array['section_slug'],
            sectionOrder: $array['section_order'],
            pageSlug: $array['page_slug'],
            pageOrder: $array['page_order'],
            resubmissionNote: $array['resubmission_note'],
            isResubmissionRequested: $array['is_resubmission_requested'],
            isResubmitted: $array['is_resubmitted']
        );
        if (array_key_exists('value', $array) && $array['value']) {
            $data->setValue(NidDTO::fromArray($array['value']));
        }

        return $data;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['value'] = null;
        if ($this->value != null) {
            $data['value'] = $this->getValue()->toArray();
        }
        $data['resubmission_note'] = $this->resubmissionNote;
        $data['is_resubmitted'] = $this->isResubmitted;
        $data['is_resubmission_requested'] = $this->isResubmissionRequested;

        return $data;
    }

    public function toArrayForApi(): array
    {
        $data = parent::toArray();
        $data['value'] = null;
        if ($this->value != null) {
            $data['value'] = $this->getValue()->toArrayForApi();
        }
        $data['resubmission_note'] = $this->resubmissionNote;
        $data['is_resubmitted'] = $this->isResubmitted;
        $data['is_resubmission_requested'] = $this->isResubmissionRequested;

        return $data;
    }

    public function getValue(): ?NidDTO
    {
        return $this->value;
    }

    /**
     * @param  NidDTO  $value
     * @return $this
     */
    public function setValue(NidDTO $value): NidStep
    {
        $this->value = $value;

        return $this;
    }

    public function addStepToApplicationData(ApplicationData $applicationData)
    {
        $section = $applicationData->getSection($this->sectionSlug) ?? new ApplicationDataNidSection();
        $section->addNidStep($this);
        $applicationData->addOrUpdateSection($section);
    }

    public function requestForResubmission(ResubmissionRequestItem $item): self
    {
        $this->resubmissionNote = $item->resubmissionNote;
        $this->isResubmissionRequested = true;

        return $this;
    }

    public function matchConditionValue(Condition $condition)
    {

    }
}
