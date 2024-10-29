<?php

namespace App\Services\Application\NextStepBuilder;

use App\Models\Application;
use App\Models\Page;
use App\Models\Section;
use App\Repositories\ApplicationLog\ApplicationLogRepositoryInterface;
use App\Repositories\Page\PageRepositoryInterface;
use App\Repositories\Section\SectionRepositoryInterface;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\ApplicationData\ApplicationDataFormSection;
use App\Services\Application\ApplicationData\ApplicationDataSection;
use App\Services\Application\Form\ConditionWrapper;
use App\Services\Application\Status;
use App\Services\Application\Step\FormSectionStep;
use App\Services\Application\Step\Step;
use App\Services\Application\Step\SuccessStep;

class ConfigNextStepBuilder implements NextStepBuilder
{
    private ApplicationData $applicationData;

    public function __construct(
        private readonly SectionRepositoryInterface $sectionRepository,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly ApplicationLogRepositoryInterface $applicationLogRepository
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function getNextStep(ApplicationData $applicationData, string $sectionSlug, string $pageSlug = null): Step
    {
        $this->applicationData = $applicationData;
        /** @var Section $nextSection */
        /** @var Page $nextPage */
        [$nextSection, $nextPage] = $this->getNextSectionAndPage($sectionSlug, $pageSlug);
        if (!$nextSection) return new SuccessStep(
            label: "আপনার আবেদন সফল হয়েছে",
            description: 'আপনার অ্যাকাউন্ট পেমেন্ট গ্রহণের জন্যে তৈরি। ভেরিফিকেশনের পরে আপনার অ্যাকাউন্ট উত্তোলন ও অন্যান্য লেনদেনের জন্য খুলে দেয়া হবে। অনুগ্রহ করে ৩ কার্যদিবস অপেক্ষা করুন।',
            sectionSlug: "success",
            sectionOrder: 14
        );
        return $nextSection->getStep($nextPage, $applicationData);
    }

    /**
     * @throws \Exception
     */
    public function getNextResubmissionStep(ApplicationData $applicationData, Application $application, string $sectionSlug, string $pageSlug = null): ?Step
    {
        $status = $application->status;
        $this->applicationData = $applicationData;
        /** @var Step $nextPage */
        [$nextSection, $nextPage] = $this->getNextResubmissionSectionAndPage($applicationData, $status, $sectionSlug, $pageSlug);
        if (!$nextSection) {
            $application->update(['status' => Status::RESUBMITTED]);
            $this->applicationLogRepository->getLatestApplicationById($application->id)->update([
                'status' => Status::RESUBMITTED
            ]);
            return new SuccessStep(
                label: "আপনার আবেদন সফল হয়েছে",
                description: 'আপনার অ্যাকাউন্ট পেমেন্ট গ্রহণের জন্যে তৈরি। ভেরিফিকেশনের পরে আপনার অ্যাকাউন্ট উত্তোলন ও অন্যান্য লেনদেনের জন্য খুলে দেয়া হবে। অনুগ্রহ করে ৩ কার্যদিবস অপেক্ষা করুন।',
                sectionSlug: "success",
                sectionOrder: 14
            );
        }
        if ($status == Status::RESUBMISSION_REQUESTED){
            $nextPage->isReSubmissionRequested = $nextPage->isReSubmissionRequested ?? false;
            if ($nextPage instanceof FormSectionStep)
            {
                foreach ($nextPage->getFields() as $field)
                {
                    $field->isResubmissionRequested = $field->isResubmissionRequested ?? false;
                }
            }
        }
        return $nextPage;
    }

    public function getInitialStep(): Step
    {
        /** @var Section $nextSection */
        /** @var Page $nextPage */
        [$nextSection, $nextPage] = $this->getSectionWithFirstPage($this->sectionRepository->getSectionByOrder(1));

        return $nextSection->getStep($nextPage);
    }
//    /**
//     * @return Step []
//     */
//    public function getAllNextSteps(string $section = null, string $page = null): array
//    {
//        $form = [];
//        $step = $this->getNextStep($section, $page);
//        while (! ($step instanceof SuccessStep)) {
//            $form[] = $step;
//            $step = $this->getNextStep($step->getSectionSlug(), $step instanceof FormSectionStep || $step instanceof NidStep || $step instanceof TradeLicenseStep ? $step->getPageSlug() : null);
//        }
//
//        return $form;
//    }

    private function getNextSectionAndPage(string $sectionSlug, string $pageSlug = null): array
    {
        /** @var Section $currentSection */
        /** @var Section $nextSection */
        $currentSection = $this->sectionRepository->getSectionBySlug($sectionSlug);

        if ($currentSection->hasMultiplePage() && ! $pageSlug) {
            throw new \Exception('Page Slug Needed');
        }

        if ($currentSection->hasMultiplePage()) {
            $currentPage = $this->pageRepository->getPageBySlug($pageSlug);
            $nextPage = $currentSection->pages()->nextOrder($currentPage)->first();
            if ($nextPage) {
                return [$currentSection, $nextPage];
            }
        }

        $nextSection = $currentSection->getNextSection();
        if (! $nextSection) {
            return [null, null];
        }

        if (! $this->isSectionVisible($nextSection)) {
            return $this->getNextSectionAndPage($nextSection->slug, $this->getSectionLastPage($nextSection)->slug);
        }

        return $this->getSectionWithFirstPage($nextSection);
    }

    private function getNextResubmissionSectionAndPage(ApplicationData $applicationData, Status $status, string $sectionSlug, string $pageSlug = null): array
    {
        /** @var ApplicationDataSection $currentSection */
        /** @var Section $nextSection */
        $currentSection = $applicationData->getSection($sectionSlug);

        if ($currentSection instanceof ApplicationDataFormSection) {
            $nextPage = $currentSection->getNextResubmissionPage($pageSlug);
            if ($nextPage) {
                return [$currentSection, $nextPage];
            }
        }
        $nextSection = $applicationData->getNextResubmissionSection($sectionSlug);

        if (! $nextSection) {
            return [null, null];
        }
        if ($status == Status::RESUBMISSION_REQUESTED)
        {
            $nextSection->isReSubmissionRequested = $nextSection->isReSubmissionRequested ?? false;
        }

        return $this->getResubmissionSectionWithFirstPage($nextSection, $status);
    }

    private function isSectionVisible($section): bool
    {
        if (empty($section->visible_if)) {
            return true;
        }
        $condition = ConditionWrapper::fromArray(json_decode($section->visible_if, true))->setApplicationData($this->applicationData);
        return $condition->isBackendVisible();
    }

    private function getSectionWithFirstPage(Section $section): array
    {
        return [$section, $section->getFirstPage()];
    }

    private function getSectionLastPage(Section $section): Page
    {
        return $section->getLastPage();
    }

    private function getResubmissionSectionWithFirstPage(ApplicationDataSection $nextSection, Status $status): array
    {
        $firstPage = $nextSection->getFirstPage();
        $firstPage->isReSubmissionRequested = $firstPage->isReSubmissionRequested ?? false;
        return [$nextSection, $firstPage];
    }
}
