<?php

namespace App\Services\Application\Step;

abstract class StepWithPage extends Step
{
    public function __construct(
        ?string $label,
        ?string $description,
        string $sectionSlug,
        int $sectionOrder,
        StepType $type,
        public readonly string $pageSlug,
        public readonly int $pageOrder,
        bool $isResubmitted = false,
        ?bool $isResubmissionRequested = null,
        bool $showReview = false
    ) {
        parent::__construct(
            type: $type,
            label: $label,
            description: $description,
            sectionSlug: $sectionSlug,
            sectionOrder: $sectionOrder,
            isReSubmissionRequested: $isResubmissionRequested,
            isResubmitted: $isResubmitted,
            showReview: $showReview
        );
    }

    public static function fromArray(array $array): static
    {
        return new static(
            label: $array['label'],
            description: $array['description'],
            sectionSlug: $array['section_slug'],
            sectionOrder: $array['section_order'],
            pageSlug: $array['page_slug'],
            pageOrder: $array['page_order']
        );
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['page_slug'] = $this->getPageSlug();
        $data['page_order'] = $this->getPageOrder();

        return $data;
    }


    /**
     * @return string
     */
    public function getPageSlug(): string
    {
        return $this->pageSlug;
    }

    /**
     * @return int|null
     */
    public function getPageOrder(): ?int
    {
        return $this->pageOrder;
    }
}
