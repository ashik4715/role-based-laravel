<?php

namespace App\Services\Application\Form\Fields;

use App\Services\Application\Form\Condition;
use App\Services\Application\Form\ConditionWrapper;
use App\Services\Application\Form\ValidationRuleException;
use App\Services\Application\Form\ValidationRules\Rule;
use App\Services\Application\Form\ValidationRules\StringValidationRule;

class StringField extends Field
{
    public function __construct(
        int $id,
        string $label,
        string $slug,
        int $order,
        private readonly ?StringValidationRule $validationRule = null,
        ?string $description = null,
        ?int $groupId = null,
        ?string $resubmissionNote = null,
        ?bool $isResubmissionRequested = null,
        bool $isResubmitted = false,
        ?ConditionWrapper $visibilityCondition = null,
        private string|null $value = null,
        ?array $visibilityDependentField = null
    ) {
        parent::__construct(
            id: $id,
            type: FieldType::STRING,
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
    public function validateAndSetValue(?string $value, string $label): self
    {
        $this->validationRule?->validate($value, $label);
        $this->value = $value;

        return $this;
    }

    /**
     * @throws ValidationRuleException
     */
    public function setValue(string $value): self
    {
        if ($this->validationRule) {
            $this->validateAndSetValue($value);
        }
        $this->value = $value;

        return $this;
    }

    public function getValue(): ?string
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
            isResubmitted: $array['is_resubmitted'] ?? false,
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
