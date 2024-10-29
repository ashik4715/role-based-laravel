<?php

namespace App\Services\Application\Step;

use App\Services\Application\DTO\SignatureDTO;

class SignatureStep extends Step
{
    use SimpleApplicationStepAdder, InvalidResubmissionRequestStep;

    public function __construct(
        ?string $label = null,
        ?string $description = null,
        ?string $sectionSlug = null,
        ?int $sectionOrder = null,
        private ?SignatureDTO $value = null
    ) {
        parent::__construct(
            type: StepType::SIGNATURE,
            label: $label,
            description: $description,
            sectionSlug: $sectionSlug,
            sectionOrder: $sectionOrder,
        );
    }

    public static function fromArray(array $array): static
    {
        $data = new static(
            label: $array['label'],
            description: $array['description'],
            sectionSlug: $array['section_slug'],
            sectionOrder: $array['section_order'],
        );
        if (array_key_exists('value', $array)) {
            $data->setValue(SignatureDTO::fromArray($array['value']));
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

    public function getValue(): ?SignatureDTO
    {
        return $this->value;
    }

    /**
     * @param  SignatureDTO  $value
     * @return $this
     */
    public function setValue(SignatureDTO $value): SignatureStep
    {
        $this->value = $value;

        return $this;
    }
}
