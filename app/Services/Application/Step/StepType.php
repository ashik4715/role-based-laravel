<?php

namespace App\Services\Application\Step;

use App\Helpers\ReverseEnum;
use App\Services\Application\Exceptions\InvalidStepTypeException;

enum StepType: string
{
    use ReverseEnum;

    case ERROR = 'error';
    case FORM_SECTION = 'form_section';
    case INFORMATION = 'information';
    case LIVELINESS = 'liveliness';
    case SELFIE = 'selfie';
    case NID = 'nid';
    case OTP = 'otp';
    case REVIEW = 'review';
    case SIGNATURE = 'signature';
    case SUCCESS = 'success';
    case SURVEY = 'survey';
    case TRADE_LICENSE = 'trade_license';

    /**
     * @throws InvalidStepTypeException
     */
    public function getStepByArray(array $array): Step
    {
        if ($this === self::OTP) {
            return OtpStep::fromArray($array);
        } elseif ($this === self::SURVEY) {
            return SurveyStep::fromArray($array);
        } elseif ($this === self::NID) {
            return NidStep::fromArray($array);
        } elseif ($this === self::SIGNATURE) {
            return SignatureStep::fromArray($array);
        } elseif ($this === self::TRADE_LICENSE) {
            return TradeLicenseStep::fromArray($array);
        } elseif ($this === self::FORM_SECTION) {
            return FormSectionStep::fromArray($array);
        } elseif ($this === self::REVIEW) {
            return ReviewStep::fromArray($array);
        }

        throw new InvalidStepTypeException();
    }
}
