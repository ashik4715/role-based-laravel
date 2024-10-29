<?php

namespace App\Services\Application\Step;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\ApplicationData\ApplicationDataNidSection;
use App\Services\Application\DTO\SelfieImagesDTO;
use App\Services\Application\Form\Condition;
use App\Services\Application\ResubmissionRequest\ResubmissionRequestItem;

class Selfie extends StepWithPage
{
    public function __construct(
        string $label,
        ?string $description,
        string $sectionSlug,
        int $sectionOrder,
        string $pageSlug,
        int $pageOrder,
        private ?SelfieImagesDTO $value = null,
        protected ?string $resubmissionNote = null,
        protected ?bool $isResubmissionRequested = null,
    ) {
        parent::__construct(
            label: $label,
            description: $description,
            sectionSlug: $sectionSlug,
            sectionOrder: $sectionOrder,
            type: StepType::SELFIE,
            pageSlug: $pageSlug,
            pageOrder: $pageOrder,
            isResubmissionRequested: $isResubmissionRequested
        );
    }

    public function setValue(SelfieImagesDTO $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): SelfieImagesDTO
    {
        return $this->value;
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
            resubmissionNote: $array['resubmission_note'] ?? null,
            isResubmissionRequested: $array['is_resubmission_requested'] ?? null
        );
        if (array_key_exists('value', $array) && $array['value']) {
            $data->setValue(SelfieImagesDTO::fromArray($array['value']));
        }
        return $data;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['value'] = null;
        if ($this->value) {
            $data['value'] = $this->value->toArray();
        }
        $data['resubmission_note'] = $this->resubmissionNote;
        $data['is_resubmission_requested'] = $this->isResubmissionRequested;
        return $data;
    }

    public function toArrayForApi(): array
    {
        $data = $this->toArray();
        if ($this->value) {
            $data['value'] = $this->value->toArrayForApi();
        }

        return $data;
    }

    public function addStepToApplicationData(ApplicationData $applicationData)
    {
        /** @var ApplicationDataNidSection $section */
        $section = $applicationData->getSection($this->sectionSlug);
        $section->addSelfieImagesStep($this);
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
