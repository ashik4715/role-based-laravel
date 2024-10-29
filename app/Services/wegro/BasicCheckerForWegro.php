<?php

namespace App\Services\wegro;

use App\Repositories\Section\SectionRepositoryInterface;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\DraftChecker;
use App\Services\Application\Status;
use App\Services\Application\Step\SurveyStep;

class BasicCheckerForWegro implements DraftChecker
{
    public function shouldStartDrafting(string $section_slug): bool
    {
       if ($section_slug == 'nid') {
           return true;
       }

        return false;
    }

    public static function shouldShowReview(string $sectionSlug, ?ApplicationData $applicationData)
    {
        if ($applicationData === null) return false;
        return $sectionSlug === 'guarantor' || $applicationData->hasSection('guarantor');
    }

    public static function isEditable($sectionSlug, $applicationStatus = Status::INITIATED): bool
    {
        $nonEditableSections = ['otp', 'farming_type'];
        if (in_array($sectionSlug, $nonEditableSections)) return false;
        return $applicationStatus == Status::INITIATED || $applicationStatus == Status::RESUBMISSION_REQUESTED;
    }

    public static function getStepCounter($slug, ?ApplicationData $applicationData): array
    {
        $steps = [
            '1' => "১",
            '2' => "২",
            '3' => "৩",
            '4' => "৪",
            '5' => "৫",
            '6' => "৬",
            '7' => "৭",
            '8' => "৮",
            '9' => "৯",
            '10' => "১০",
            '11' => "১১",
            '12' => "১২",
            '13' => "১৩",
            '14' => "১৪",
            '15' => "১৫",
        ];
        /** @var SectionRepositoryInterface $sectionRepo */
        $sectionRepo = app(SectionRepositoryInterface::class);
        $currentSection = $sectionRepo->getSectionBySlug($slug);
        /** @var SurveyStep $farmingType */
        $farmingType = $applicationData?->getSection("farming_type")?->getFirstPage();
        $totalStep = $sectionRepo->getSectionCount();
        $currentSectionCount = (int) $currentSection->order;
        if ($farmingType && !in_array("cattle", $farmingType->field->getPlainValue())) {
            $totalStep = $totalStep - 1;
            if ($currentSectionCount > 9) $currentSectionCount = $currentSectionCount - 1;
        }
        return [
            'total_step' => $steps[$totalStep],
            'current_step' => $steps[$currentSectionCount]
        ];
    }
}
