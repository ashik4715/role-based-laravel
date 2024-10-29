<?php

namespace App\Services\Application\Form\Fields;

use App\Services\Application\DTO\BdMobile;
use App\Services\Application\Form\Condition;
use App\Services\Application\Form\ValidationRuleException;
use App\Services\Application\Form\ValidationRules\Rule;
use App\Services\Application\Form\ValidationRules\StringValidationRule;

class MobileField extends Field
{
    public function __construct(
        int $id,
        string $label,
        string $slug,
        string $order,
        private readonly ?Rule $validationRule = null,
        ?string $description = null,
        ?int $groupId = null,
        ?string $resubmissionNote = null,
        ?bool $isResubmissionRequested = null,
        private ?BdMobile $value = null
    ) {
        parent::__construct(
            id: $id,
            type: FieldType::MOBILE,
            label: $label,
            slug: $slug,
            order: $order,
            description: $description,
            groupId: $groupId,
            resubmissionNote: $resubmissionNote,
            isResubmissionRequested: $isResubmissionRequested,
        );
    }

    /**
     * @throws ValidationRuleException
     */
    public function validateAndSetValue(?BdMobile $value, string $label): self
    {
        $this->validationRule?->validateCommonRules($value?->getFullNumber(), $label);
        $this->value = $value;

        return $this;
    }

    public function setValue(BdMobile $value): self
    {
        $this->validateAndSetValue($value);

        return $this;
    }

    public function getValue(): BdMobile
    {
        return $this->value;
    }

    protected function getValidationRule(): ?Rule
    {
        return $this->validationRule;
    }

    protected function getValueForArray(): array|string|int|float|null
    {
        return $this->value?->getFullNumber();
    }

    /**
     * @throws \Exception
     */
    public static function fromArray(array $array): static
    {
        $value = Field::getValueFromArray($array);
        $rules = Field::getRulesFromArray($array);
        return new static(
            id: $array['id'],
            label: $array[Field::LABEL_ARRAY_KEY],
            slug: $array[Field::SLUG_ARRAY_KEY],
            order: $array['order'],
            validationRule: $rules == null ? null : StringValidationRule::fromArray($rules),
            description: $array[Field::DESCRIPTION_ARRAY_KEY],
            groupId: $array['group_id'],
            resubmissionNote: $array['resubmission_note'] ?? null,
            isResubmissionRequested: $array['is_resubmission_requested'] ?? null,
            value: $value == null ? $value : new BdMobile($value),
        );
    }

    public function toArrayForApi(): array
    {
        $data = parent::toArrayForApi();
        $data[self::VALUE_ARRAY_KEY] = $this->value?->getNumberWithoutPrefix();

        return $data;
    }

    public function getPlainValue(): array|string|int|float|null
    {
        return $this->value?->getFullNumber();
    }

    public function matchConditionValue(Condition $condition): bool
    {
        return $this->value === $condition->value;
    }
}
