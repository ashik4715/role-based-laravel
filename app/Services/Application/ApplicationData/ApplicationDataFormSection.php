<?php

namespace App\Services\Application\ApplicationData;

use App\Services\Application\Exceptions\FieldNotFoundException;
use App\Services\Application\Form\Condition;
use App\Services\Application\ResubmissionRequest\ResubmissionRequestItem;
use App\Services\Application\Status;
use App\Services\Application\Step\FormSectionStep;
use App\Services\Application\Step\Step;
use Exception;

class ApplicationDataFormSection extends ApplicationDataSection
{
    /**
     * @param  FormSectionStep[]  $pages
     */
    public function __construct(public array $pages = [], ?bool $isReSubmissionRequested = null, bool $isResubmitted = false)
    {
        parent::__construct($isReSubmissionRequested, $isResubmitted);
    }

    public static function fromArray(array $array): static
    {
        $pages = [];
        foreach ($array['pages'] as $key => $value) {
            $pages[$key] = FormSectionStep::fromArray($value);
        }

        return new static(pages: $pages, isReSubmissionRequested: $array['is_resubmission_requested'], isResubmitted: $array['is_resubmitted']);
    }

    public function getSectionSlug(): string
    {
        return $this->getAPage()->sectionSlug;
    }

    public function addOrUpdatePage(FormSectionStep $step): self
    {
        $this->pages[$step->pageSlug] = $step;

        return $this;
    }

    public function toArray(): array
    {
        $page = $this->getAPage();

        return [
            'section_slug' => $page->sectionSlug,
            'section_order' => $page->sectionOrder,
            'type' => $page->type->value,
            'pages' => array_map(function (FormSectionStep $step) {
                return $step->toArray();
            }, $this->pages),
            'is_resubmission_requested' => $this->isReSubmissionRequested,
            'is_resubmitted' => $this->isResubmitted
        ];
    }

    /**
     * @throws Exception
     */
    public function toArrayForApi(): array
    {
        $page = $this->getAPage();
        $data = [
            'section_slug' => $page->sectionSlug,
            'section_order' => $page->sectionOrder,
            'type' => $page->type->value,
        ];

        foreach ($this->pages as $step) {
            $data['pages'][] = $step->toArrayForApi();
        }

        return $data;
    }

    public function toArrayForReview(Status $status): array
    {
        $page = $this->getAPage();
        $isResubmitRequested = $this->isReSubmissionRequested;

        if ($status == Status::RESUBMISSION_REQUESTED) {
            $isResubmitRequested = ($this->isReSubmissionRequested !== null) ? $this->isReSubmissionRequested : false;
        } elseif ($this->isReSubmissionRequested === null) {
            $isResubmitRequested = null;
        }

        if ($status == Status::SUBMITTED || $status == Status::APPROVED || $status == Status::REJECTED || $status == Status::RESUBMITTED) $isResubmitRequested = false;

        $data = [
            'section_slug' => $page->sectionSlug,
            'section_order' => $page->sectionOrder,
            'type' => $page->type->value,
            'is_resubmission_requested' => $isResubmitRequested
        ];

        foreach ($this->pages as $step) {
            $data['pages'][] = $step->toArrayForReview($status);
        }
        return $data;
    }

    public function toArrayForAdmin(): array
    {
        $page = $this->getAPage();
        $data = [
            'section_slug' => $page->sectionSlug,
            'section_order' => $page->sectionOrder,
            'type' => $page->type->value,
        ];

        foreach ($this->pages as $key => $step) {
            $data['pages'][$key] = $step->toArrayForAdmin();
        }

        return $data;
    }

    private function getAPage(): FormSectionStep
    {
        if (empty($this->pages)) {
            throw new Exception('no page has been added yet.');
        }

        return array_values($this->pages)[0];
    }

    public function getStep(?string $pageSlug): ?FormSectionStep
    {
        return $this->pages[$pageSlug] ?? null;
    }

    /**
     * @throws FieldNotFoundException
     */
    public function requestForResubmission(ResubmissionRequestItem $item)
    {
        $this->isReSubmissionRequested = true;
        $this->getStep($item->pageSlug)->requestForResubmission($item);
    }

    public function matchConditionValue(Condition $condition): bool
    {
        return $this->getStep($condition->page)->matchConditionValue($condition);
    }

    public function getSectionOrder(): int
    {
        return $this->getAPage()->sectionOrder;
    }

    public function isReSubmissionRequested(): ?bool
    {
        return $this->getAPage()->isReSubmissionRequested;
    }

    public function getFirstPage(): Step
    {
        $min = count($this->pages);
        foreach ($this->pages as $page)
        {
            if ($page->pageOrder <= $min && $page->isReSubmissionRequested) return $page;
        }
    }

    public function getNextResubmissionPage($key): ?Step
    {
        $currentPage = $this->getStep($key);
        $min = count($this->pages);
        foreach ($this->pages as $page)
        {
            if ($currentPage->pageOrder < $page->pageOrder && $page->pageOrder < $min && $page->isReSubmissionRequested) return $page;
        }

        return null;
    }

    public function getPages()
    {
        return $this->pages;
    }
}
