<?php

namespace App\Services\Application;

use App\Services\Application\Step\StepType;

trait HasStepType
{
    /**
     * @return bool
     */
    public function isFormSection(): bool
    {
        return $this->type === StepType::FORM_SECTION;
    }

    /**
     * @return bool
     */
    public function isSurvey(): bool
    {
        return $this->type === StepType::SURVEY;
    }

    /**
     * @return bool
     */
    public function isOtp(): bool
    {
        return $this->type === StepType::OTP;
    }

    public function isInformation(): bool
    {
        return $this->type === StepType::INFORMATION;
    }

    /**
     * @return bool
     */
    public function isSignature(): bool
    {
        return $this->type === StepType::SIGNATURE;
    }

    public function isReview(): bool
    {
        return $this->type === StepType::REVIEW;
    }

    public function isError(): bool
    {
        return $this->type === StepType::ERROR;
    }

    /**
     * @return bool
     */
    public function isNotFormSection(): bool
    {
        return ! $this->isFormSection();
    }

    public function isNotNID(): bool
    {
        return ! $this->isNID();
    }

    public function isNID(): bool
    {
        return $this->type === StepType::NID;
    }

    public function isSelfie(): bool
    {
        return $this->type === StepType::SELFIE;
    }

    public function isTradeLicense(): bool
    {
        return $this->type === StepType::TRADE_LICENSE;
    }

    public function isNotTradeLicense(): bool
    {
        return ! $this->isTradeLicense();
    }

    public function isSuccess(): bool
    {
        return $this->type === StepType::SUCCESS;
    }
}
