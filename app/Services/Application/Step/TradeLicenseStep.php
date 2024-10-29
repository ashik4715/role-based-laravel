<?php

namespace App\Services\Application\Step;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\ApplicationData\ApplicationDataTradeLicenseSection;
use App\Services\Application\DTO\TradeLicenseDTO;
use App\Services\Application\Form\Condition;

class TradeLicenseStep extends StepWithPage
{
    use InvalidResubmissionRequestStep;
    public function __construct(
        ?string $label,
        ?string $description,
        string $sectionSlug,
        int $sectionOrder,
        string $pageSlug,
        int $pageOrder,
        private ?TradeLicenseDTO $value = null
    ) {
        parent::__construct(
            label: $label,
            description: $description,
            sectionSlug: $sectionSlug,
            sectionOrder: $sectionOrder,
            type: StepType::TRADE_LICENSE,
            pageSlug: $pageSlug,
            pageOrder: $pageOrder,
        );
    }

    public static function fromArray(array $array): static
    {
        $data = new static(
            label: $array['label'],
            description: $array['description'],
            sectionSlug: $array['section_slug'],
            sectionOrder: $array['section_order'],
            pageSlug: $array['page_slug'],
            pageOrder: $array['page_order'],
        );
        if (array_key_exists('value', $array) && $array['value']) {
            $data->setValue(TradeLicenseDTO::fromArray($array['value']));
        }

        return $data;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        if ($this->value != null) {
            $data['value'] = $this->getValue()->toArray();
        }

        return $data;
    }

    public function getValue(): ?TradeLicenseDTO
    {
        return $this->value;
    }

    /**
     * @param  TradeLicenseDTO  $value
     * @return TradeLicenseStep
     */
    public function setValue(TradeLicenseDTO $value): TradeLicenseStep
    {
        $this->value = $value;

        return $this;
    }

    public function addStepToApplicationData(ApplicationData $applicationData)
    {
        $section = $applicationData->getSection($this->sectionSlug) ?? new ApplicationDataTradeLicenseSection();
        $section->addOrUpdatePage($this);
        $applicationData->addOrUpdateSection($section);
    }

    public function matchConditionValue(Condition $condition)
    {
        // TODO: Implement matchConditionValue() method.
    }
}
