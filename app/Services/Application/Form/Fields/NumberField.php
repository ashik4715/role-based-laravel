<?php

namespace App\Services\Application\Form\Fields;

use App\Services\Application\Form\Condition;
use App\Services\Application\Form\ConditionWrapper;
use App\Services\Application\Form\ValidationRuleException;
use App\Services\Application\Form\ValidationRules\NumberValidationRule;
use App\Services\Application\Form\ValidationRules\Rule;

class NumberField extends Field
{
    public function __construct(
        int $id,
        string $label,
        string $slug,
        int $order,
        private readonly ?NumberValidationRule $validationRule = null,
        ?string $description = null,
        ?int $groupId = null,
        ?string $resubmissionNote = null,
        bool $isResubmitted = false,
        ?bool $isResubmissionRequested = null,
        ?ConditionWrapper $visibilityCondition = null,
        private int|float|null $value = null,
        ?array $visibilityDependentField = null
    ) {
        parent::__construct(
            id: $id,
            type: FieldType::NUMBER,
            label: $label,
            slug: $slug,
            order: $order,
            description: $description,
            groupId: $groupId,
            visibilityCondition: $visibilityCondition,
            resubmissionNote: $resubmissionNote,
            isResubmissionRequested: $isResubmissionRequested,
            isResubmitted: $isResubmitted,
            visibilityDependentField: $visibilityDependentField
        );
    }

    /**
     * @throws ValidationRuleException
     */
    public function validateAndSetValue(int|float $value, string $label): self
    {
        $this->validationRule?->validate($value, $label);
        $this->value = $value;

        return $this;
    }

    public function getValue(): float|int|null
    {
        return $this->value;
    }

    protected function getValidationRule(): ?Rule
    {
        return $this->validationRule;
    }

    public function getPlainValue(): array|string|int|float|null
    {
        return $this->value;
    }

    public static function fromArray(array $array): static
    {
        $rule = Field::getRulesFromArray($array);

        return new static(
            id: $array['id'],
            label: $array[Field::LABEL_ARRAY_KEY],
            slug: $array[Field::SLUG_ARRAY_KEY],
            order: $array['order'],
            validationRule: $rule == null ? null : NumberValidationRule::fromArray($rule),
            description: $array[Field::DESCRIPTION_ARRAY_KEY],
            groupId: $array['group_id'],
            resubmissionNote: $array['resubmission_note'],
            isResubmitted: $array['is_resubmitted'],
            isResubmissionRequested: $array['is_resubmission_requested'],
            visibilityCondition: isset($array['visible_if']) ? ConditionWrapper::fromArray($array['visible_if']): null,
            value: Field::getValueFromArray($array),
            visibilityDependentField: $array['visibility_dependent_field'] ?? null
        );
    }

    public function matchConditionValue(Condition $condition): bool
    {
        return $this->value === $condition->value;
    }
}
