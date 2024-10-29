<?php

namespace App\Services\Application\Form\Fields;

use App\Services\Application\Form\Condition;
use App\Services\Application\Form\ConditionWrapper;
use App\Services\Application\Form\ValidationRuleException;
use App\Services\Application\Form\ValidationRules\DateValidationRule;
use App\Services\Application\Form\ValidationRules\Rule;
use Carbon\Carbon;

class DateField extends Field
{
    public function __construct(
        int $id,
        string $label,
        string $slug,
        int $order,
        private readonly ?DateValidationRule $validationRule = null,
        ?string $description = null,
        ?int $groupId = null,
        ?string $resubmissionNote = null,
        ?bool $isResubmissionRequested = null,
        bool $isResubmitted = false,
        ?ConditionWrapper $visibilityCondition = null,
        private ?Carbon $value = null
    ) {
        parent::__construct(
            id: $id,
            type: FieldType::DATE,
            label: $label,
            slug: $slug,
            order: $order,
            description: $description,
            groupId: $groupId,
            visibilityCondition: $visibilityCondition,
            resubmissionNote: $resubmissionNote,
            isResubmissionRequested: $isResubmissionRequested,
            isResubmitted: $isResubmitted
        );
    }

    public function setValue(Carbon $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): ?Carbon
    {
        return $this->value;
    }

    public static function fromArray(array $array): static
    {
        $rule = Field::getRulesFromArray($array);
        $value = Field::getValueFromArray($array);

        return new static(
            id: $array['id'],
            label: $array[Field::LABEL_ARRAY_KEY],
            slug: $array[Field::SLUG_ARRAY_KEY],
            order: $array['order'],
            validationRule: $rule == null ? null : DateValidationRule::fromArray($rule),
            description: $array[Field::DESCRIPTION_ARRAY_KEY],
            groupId: $array['group_id'],
            resubmissionNote: $array['resubmission_note'] ?? null,
            isResubmissionRequested: $array['is_resubmission_requested'] ?? null,
            isResubmitted: $array['is_resubmitted'] ?? false,
            visibilityCondition: isset($array['visible_if']) ? ConditionWrapper::fromArray($array['visible_if']): null,
            value: $value == null ? null : Carbon::parse($value)
        );
    }

    /**
     * @param  Carbon  $value
     * @return $this
     *
     * @throws ValidationRuleException
     */
    public function validateAndSetValue(Carbon $value, string $label): self
    {
        $this->validationRule?->validate($value);
        $this->value = $value;

        return $this;
    }

    protected function getValidationRule(): ?Rule
    {
        return $this->validationRule;
    }

    public function getPlainValue(): array|string|int|float|null
    {
        return $this->value?->toDateString();
    }

    public function matchConditionValue(Condition $condition): bool
    {
        return $this->value->toDateString() === $condition->value;
    }
}
