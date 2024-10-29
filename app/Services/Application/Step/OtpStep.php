<?php

namespace App\Services\Application\Step;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\DTO\BdMobile;
use App\Services\Application\Form\Condition;

class OtpStep extends Step
{
    use SimpleApplicationStepAdder, InvalidResubmissionRequestStep;

    public function __construct(
        ?string                           $label = null,
        ?string                           $description = null,
        ?string                           $sectionSlug = null,
        ?int                              $sectionOrder = null,
        private ?BdMobile                 $value = null,
        public bool                       $showReview = false,
        public bool                       $isEditable = false,
        private readonly ?ApplicationData $applicationData = null,
    ) {
        parent::__construct(
            type: StepType::OTP,
            label: $label,
            description: $description,
            sectionSlug: $sectionSlug,
            sectionOrder: $sectionOrder,
            showReview: $showReview,
        );
    }

    /**
     * @throws \Exception
     */
    public static function fromArray(array $array): static
    {
        $data = new static(
            label: $array['label'],
            description: $array['description'],
            sectionSlug: $array['section_slug'],
            sectionOrder: $array['section_order'],
        );
        if (array_key_exists('value', $array)) {
            $data->setValue(new BdMobile($array['value']));
        }

        return $data;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        if ($this->value != null) {
            $data['value'] = $this->getValue()->getFullNumber();
        }
        if ($data['section_slug'] == 'otp') $data['input_otp_number'] = true;
        else if ($data['section_slug'] == 'guarantor-otp') {
            $data['input_otp_number'] = false;
        }
        return $data;
    }

    public function toArrayForApi(): array
    {
        $data = parent::toArray();
        if ($this->value != null) {
            $data['value'] = $this->getValue()->getFullNumber();
        }
        if ($data['section_slug'] == 'otp') $data['input_otp_number'] = true;
        else if ($data['section_slug'] == 'guarantor-otp') {
            $data['input_otp_number'] = false;
            $data['guarantor_mobile'] = $this->getGuarantorMobile();
        }
        return $data;
    }

    public function getValue(): ?BdMobile
    {
        return $this->value;
    }

    /**
     * @param  BdMobile  $value
     * @return $this
     */
    public function setValue(BdMobile $value): OtpStep
    {
        $this->value = $value;

        return $this;
    }

    public function matchConditionValue(Condition $condition)
    {

    }

    private function getGuarantorMobile()
    {
        /** @var FormSectionStep $step */
        $step = $this->applicationData->getStep(sectionSlug: "guarantor", pageSlug: "guarantor_info");
        return substr($step->getField("guarantor_phone_number")->getPlainValue(),-11);
    }
}
