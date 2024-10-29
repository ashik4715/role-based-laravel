<?php

namespace App\Services\Application\ApplicationData;

use App\Services\Application\Exceptions\FieldNotFoundException;
use App\Services\Application\Exceptions\InvalidResubmissionRequestException;
use App\Services\Application\Exceptions\InvalidSectionDataException;
use App\Services\Application\Exceptions\InvalidStepTypeException;
use App\Services\Application\Form\Condition;
use App\Services\Application\ResubmissionRequest\ResubmissionRequestItem;
use App\Services\Application\Status;
use App\Services\Application\Step\FormSectionStep;
use App\Services\Application\Step\NidStep;
use App\Services\Application\Step\Selfie;
use App\Services\Application\Step\Step;

class ApplicationDataNidSection extends ApplicationDataSection
{
    const NID_STEP_SLUG = 'nid_images';
    const PERSONAL_INFO_SLUG = 'farmer_info';
    const SELFIE_STEP_SLUG = 'selfie_images';

    public function __construct(
        private ?NidStep $nidStep = null,
        private ?FormSectionStep $personalInfo = null,
        private ?Selfie $selfieStep = null,
        ?bool $isReSubmissionRequested = null,
        bool $isResubmitted = false
    )
    {
        parent::__construct($isReSubmissionRequested, $isResubmitted);
    }

    public function getSectionSlug(): string
    {
        if (is_null($this->nidStep)) {
            throw new InvalidSectionDataException('No nid data found');
        }

        return $this->nidStep->sectionSlug;
    }

    public function getEnglishName(): string
    {
        return $this->personalInfo->getField('nid_name_english')->getValue();
    }

    public function getUserImage(): string
    {
        return $this->selfieStep->getValue()->getUserImage();
    }

    /**
     * @throws InvalidResubmissionRequestException
     * @throws FieldNotFoundException
     */
    public function requestForResubmission(ResubmissionRequestItem $item)
    {
        if ($item->pageSlug !== self::PERSONAL_INFO_SLUG) {
            throw new InvalidResubmissionRequestException();
        }

        $this->isReSubmissionRequested = true;
        $this->personalInfo->requestForResubmission($item);
    }

    /**
     * @throws InvalidStepTypeException
     */
    public function getStep(?string $pageSlug): ?Step
    {
        if ($pageSlug === self::NID_STEP_SLUG) {
            return $this->nidStep;
        }
        if ($pageSlug === self::PERSONAL_INFO_SLUG) {
            return $this->personalInfo;
        }
        if ($pageSlug === self::SELFIE_STEP_SLUG) {
            return $this->selfieStep;
        }
        if ($pageSlug === 'selfie_instructions') {
            return null;
        }

        throw new InvalidStepTypeException('Page slug not found', 404);
    }

    public function toArray(): array
    {
        $data = [
            'section_slug' => $this->nidStep->sectionSlug,
            'section_order' => $this->nidStep->sectionOrder,
            'type' => $this->nidStep->type->value,
            'description' => $this->nidStep->description,
            'is_resubmission_requested' => $this->isReSubmissionRequested,
            'is_resubmitted' => $this->isResubmitted,
            self::NID_STEP_SLUG => $this->nidStep->toArray(),
        ];
        if ($this->personalInfo) {
            $data[self::PERSONAL_INFO_SLUG] = $this->personalInfo->toArray();
        }

        if ($this->selfieStep) {
            $data[self::SELFIE_STEP_SLUG] = $this->selfieStep->toArray();
        }

        return $data;
    }

    public static function fromArray(array $array): static
    {
        $personalInfo = isset($array[self::PERSONAL_INFO_SLUG])
            ? FormSectionStep::fromArray([
                'label' => $array[self::PERSONAL_INFO_SLUG]['label'],
                'description' => $array[self::PERSONAL_INFO_SLUG]['description'],
                'section_slug' => $array[self::PERSONAL_INFO_SLUG]['section_slug'],
                'section_order' => $array[self::PERSONAL_INFO_SLUG]['section_order'],
                'page_slug' => self::PERSONAL_INFO_SLUG,
                'page_order' => $array[self::PERSONAL_INFO_SLUG]['page_order'],
                'values' => $array[self::PERSONAL_INFO_SLUG]['values'],
                'is_resubmission_requested' => $array[self::PERSONAL_INFO_SLUG]['is_resubmission_requested'],
                'is_resubmitted' => $array[self::PERSONAL_INFO_SLUG]['is_resubmitted'],
            ])
            : null;

        $selfieImages = isset($array[self::SELFIE_STEP_SLUG])
            ? Selfie::fromArray([
                'label' => $array[self::SELFIE_STEP_SLUG]['label'],
                'description' => $array[self::SELFIE_STEP_SLUG]['description'],
                'section_slug' => $array[self::SELFIE_STEP_SLUG]['section_slug'],
                'section_order' => $array[self::SELFIE_STEP_SLUG]['section_order'],
                'page_slug' => self::SELFIE_STEP_SLUG,
                'page_order' => $array[self::SELFIE_STEP_SLUG]['page_order'],
                'value' => $array[self::SELFIE_STEP_SLUG]['value'],
                'is_resubmission_requested' => $array[self::SELFIE_STEP_SLUG]['is_resubmission_requested'],
                'is_resubmitted' => $array[self::SELFIE_STEP_SLUG]['is_resubmitted'],
            ])
            : null;
        return new static(
            NidStep::fromArray($array[self::NID_STEP_SLUG]),
            $personalInfo,
            $selfieImages,
            $array['is_resubmission_requested'],
            $array['is_resubmitted']
        );
    }

    public function toArrayForApi(): array
    {
        $data = [
            'section_slug' => $this->nidStep->sectionSlug,
            'section_order' => $this->nidStep->sectionOrder,
            'type' => $this->nidStep->type->value,
            'description' => $this->nidStep->description,
            self::NID_STEP_SLUG => $this->nidStep->toArrayForApi(),
        ];

        if ($this->personalInfo) {
            $data[self::PERSONAL_INFO_SLUG] = $this->personalInfo->toArrayForApi();
        }

        if ($this->selfieStep) {
            $data[self::SELFIE_STEP_SLUG] = $this->selfieStep->toArrayForApi();
        }

        return $data;
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

        $data = [
            'section_slug' => $this->nidStep->sectionSlug,
            'section_order' => $this->nidStep->sectionOrder,
            'type' => $this->nidStep->type->value,
            'description' => $this->nidStep->description,
            self::NID_STEP_SLUG => $this->nidStep->toArrayForApi(),
            'is_resubmission_requested' => $isResubmitRequested
        ];

        if ($this->personalInfo) {
            $data[self::PERSONAL_INFO_SLUG] = $this->personalInfo->toArrayForReview($status);
        }

        if ($this->selfieStep) {
            $data[self::SELFIE_STEP_SLUG] = $this->selfieStep->toArrayForApi();
        }

        return $data;
    }

    public function toArrayForAdmin(): array
    {
        $data = [
            'section_slug' => $this->nidStep->sectionSlug,
            'section_order' => $this->nidStep->sectionOrder,
            'type' => $this->nidStep->type->value,
            'description' => $this->nidStep->description,
            self::NID_STEP_SLUG => $this->nidStep->toArrayForApi(),
        ];

        if ($this->personalInfo) {
            $data[self::PERSONAL_INFO_SLUG] = $this->personalInfo->toArrayForAdmin();
        }

        if ($this->selfieStep) {
            $data[self::SELFIE_STEP_SLUG] = $this->selfieStep->toArrayForApi();
        }

        return $data;
    }

    public function addOrUpdatePage(FormSectionStep $step): self
    {
        $this->addPersonalInfoStep($step);

        return $this;
    }

    public function addNidStep(NidStep $step): self
    {
        $this->nidStep = $step;

        return $this;
    }

    public function addPersonalInfoStep(FormSectionStep $personalInfoStep): self
    {
        $this->personalInfo = $personalInfoStep;

        return $this;
    }

    public function addSelfieImagesStep(Selfie $selfieStep): self
    {
        $this->selfieStep = $selfieStep;

        return $this;
    }

    public function matchConditionValue(Condition $condition)
    {

    }

    public function getSectionOrder(): int
    {
        return $this->nidStep->sectionOrder;
    }

    public function isReSubmissionRequested(): ?bool
    {
        return $this->personalInfo->isReSubmissionRequested;
    }

    public function getFirstPage(): Step
    {
        return $this->personalInfo;
    }

    public function getPages()
    {
        return [$this->personalInfo];
    }
}
