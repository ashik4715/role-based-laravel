<?php

namespace App\Services\Application\Form\Repeat;

use App\Services\Application\ApplicationData\ApplicationData;

interface LabelRepeatGenerator
{
    public function generate(?ApplicationData $applicationData): array;
}
