<?php

namespace App\Services\Application;

trait SectionPageChecker
{
    public function sectionHasPage($sectionSlug): bool
    {
        $nonPageableSections = ['otp', 'ownership_type', 'account_type', 'trade_license_survey', 'bank_account_survey'];

        return ! empty($sectionSlug) && ! in_array($sectionSlug, $nonPageableSections);
    }
}
