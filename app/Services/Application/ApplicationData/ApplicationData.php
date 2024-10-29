<?php

namespace App\Services\Application\ApplicationData;

use App\Helpers\JsonAndArrayAble;
use App\Repositories\Section\SectionRepositoryInterface;
use App\Services\Application\DTO\UserInfo;
use App\Services\Application\ResubmissionRequest\ResubmissionRequestItem;
use App\Services\Application\Status;
use App\Services\Application\Step\Step;
use App\Services\wegro\BasicCheckerForWegro;

class ApplicationData extends JsonAndArrayAble
{
    /**
     * @param  ApplicationDataSection[]  $sections
     */
    public function __construct(private array $sections = [])
    {
    }

    public function addOrUpdateSection(ApplicationDataSection $applicationDataSection): self
    {
        $this->sections[$applicationDataSection->getSectionSlug()] = $applicationDataSection;

        return $this;
    }

    public function getSection(string $key): ?ApplicationDataSection
    {
        return $this->sections[$key] ?? null;
    }

    public function hasSection(string $key): bool
    {
        return isset($this->sections[$key]);
    }

    public function toArray(): array
    {
        return array_map(function (ApplicationDataSection $applicationDataSection) {
            return $applicationDataSection->toArray();
        }, $this->sections);
    }

    public function toArrayForApi(): array
    {
        $data = [];
        foreach ($this->sections as $section) {
            $data[] = $section->toArrayForApi();
        }

        return $data;
    }

    public function toArrayForAdmin(): array
    {
        $data = [];
        foreach ($this->sections as $slug => $section) {
            $data[$slug] = $section->toArrayForAdmin();
        }

        return $data;
    }

    public function toArrayForReview(Status $status = Status::INITIATED): array
    {
        $data = [];
        foreach ($this->sections as $sectionSlug => $section) {
            if ($sectionSlug === 'otp' || $sectionSlug === 'review' || $sectionSlug == "guarantor-otp") {
                continue;
            }
            if ($section instanceof ApplicationDataGenericSection) $sectionData = $section->toArrayForApi();
            else $sectionData = $section->toArrayForReview($status);
            $sectionData['is_editable'] = BasicCheckerForWegro::isEditable($sectionSlug, $status);
            $data[] = $sectionData;
        }

        return $data;
    }

    public function getUserInfo(): UserInfo
    {
        $userName = null;
        $userImage = null;
        $mobile = null;
        $counter = 0;
        foreach ($this->sections as $section) {
            if ($section->getSectionSlug() === 'otp') {
                /** @var ApplicationDataGenericSection $section */
                $mobile = $section->step->getValue();
                $counter++;
            } elseif ($section->getSectionSlug() === 'nid') {
                /** @var ApplicationDataNidSection $section */
                $userName = $section->getEnglishName();
                $userImage = $section->getUserImage();
                $counter++;
            }

            if ($counter === 2) {
                break;
            }
        }

        return new UserInfo($userName, $mobile, $userImage);
    }

    public function getStep(string $sectionSlug, ?string $pageSlug): ?Step
    {
        $section = $this->getSection($sectionSlug);
        if ($section === null) {
            return null;
        }

        return $section->getStep($pageSlug);
    }

    public static function fromArray(array $array): static
    {
        $sections = [];
        foreach ($array as $key => $value) {
            if ($key === 'nid') {
                $sections[$key] = ApplicationDataNidSection::fromArray($value);
            } elseif (array_key_exists('pages', $value)) {
                $sections[$key] = ApplicationDataFormSection::fromArray($value);
            } else {
                $sections[$key] = ApplicationDataGenericSection::fromArray($value);
            }
        }

        return new static($sections);
    }

    /**
     * @param  array<ResubmissionRequestItem>  $items
     */
    public function requestForResubmission(array $items): self
    {
        foreach ($items as $item) {
            $this->getSection($item->sectionSlug)->requestForResubmission($item);
            foreach ($this->sections as $section){
                if ($item->sectionSlug != $section->getSectionSlug()) {
                    $section->isResubmitted = false;
                    $pages = null;
                    if (($section instanceof ApplicationDataGenericSection)) continue;
                    $pages = $section->getPages();
                    foreach ($pages as $page)
                    {
                        $page->isResubmitted = false;
                        foreach ($page->getFields() as $field)
                        {
                            $field->isResubmitted = false;
                        }
                    }
                }
            }
        }

        return $this;
    }

    public function getLastSection(): ApplicationDataSection
    {
        $max = 0;
        $lastSection = null;
        foreach ($this->sections as $section) {
            $sectionOrder = $section->getSectionOrder();
            if ($sectionOrder > $max) {
                $max = $sectionOrder;
                $lastSection = $section;
            }
        }

        return $lastSection;
    }
    public function getProgressPercentage(Status $status): int
    {
        $sectionCount = app(SectionRepositoryInterface::class)->getSectionCount();
        if ($status == Status::RESUBMISSION_REQUESTED)
        {
            $cnt = 0;
            foreach ($this->sections as $section){
                if ($section->isReSubmissionRequested()) $cnt++;
            }
            return (int)($cnt ? $cnt * 10 : 100);
        }
        $lastSection = $this->getLastSection();

        $progress = (int)($lastSection->getSectionOrder()*100 / ($sectionCount - 1));
        return min($progress, 100);
    }

    public function getNextResubmissionSection($key): ?ApplicationDataSection
    {
        $currentSection = $this->sections[$key];
        $currentSectionOrder = $currentSection->getSectionOrder();
        $minOrder = count($this->sections);
        $minSectionSlug = null;

        foreach ($this->sections as $section) {
            $sectionOrder = $section->getSectionOrder();
            if ($sectionOrder > $currentSectionOrder && $sectionOrder < $minOrder && $section->isReSubmissionRequested()) {
                $minOrder = $sectionOrder;
                $minSectionSlug = $section->getSectionSlug();
            }
        }
        if (!$minSectionSlug) {
            foreach ($this->sections as $section) {
                if ($section->isReSubmissionRequested()) return $section;
            }
        }
        return $minSectionSlug ? $this->sections[$minSectionSlug] : null;
    }

    public function getAddressInfo()
    {

    }
}
