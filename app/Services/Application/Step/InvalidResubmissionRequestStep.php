<?php

namespace App\Services\Application\Step;

use App\Services\Application\Exceptions\InvalidResubmissionRequestException;
use App\Services\Application\ResubmissionRequest\ResubmissionRequestItem;

trait InvalidResubmissionRequestStep
{
    public function requestForResubmission(ResubmissionRequestItem $item)
    {
        throw new InvalidResubmissionRequestException($this->label . ' can\'t be requested for resubmission');
    }
}
