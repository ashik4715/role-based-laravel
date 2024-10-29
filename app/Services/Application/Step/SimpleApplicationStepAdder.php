<?php

namespace App\Services\Application\Step;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\ApplicationData\ApplicationDataGenericSection;

trait SimpleApplicationStepAdder
{
    public function addStepToApplicationData(ApplicationData $applicationData): void
    {
        $applicationData->addOrUpdateSection(new ApplicationDataGenericSection($this));
    }
}
