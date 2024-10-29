<?php

namespace App\Services\Application\ApplicationData;

use App\Helpers\JsonAndArrayAble;
use App\Services\Application\ResubmissionRequest\ResubmissionRequestItem;
use App\Services\Application\Step\FormSectionStep;
use App\Services\Application\Step\Step;

abstract class ApplicationDataSection extends JsonAndArrayAble
{
    public function __construct(
        public ?bool $isReSubmissionRequested = null,
        public bool $isResubmitted = false
    )
    {
    }

    /**
     * @return string
     */
    abstract public function getSectionSlug(): string;

    abstract public function requestForResubmission(ResubmissionRequestItem $item);

    abstract public function getSectionOrder(): int;

    abstract public function isReSubmissionRequested(): ?bool;

    abstract public function getFirstPage(): Step;

    abstract public function getStep(?string $pageSlug): ?Step;

    abstract public function getPages();
}
