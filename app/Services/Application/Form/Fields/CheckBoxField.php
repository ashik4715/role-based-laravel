<?php

namespace App\Services\Application\Form\Fields;

use App\Services\Application\Form\Condition;
use App\Services\Application\Form\ConditionWrapper;
use App\Services\Application\Form\ValidationRuleException;
use App\Services\Application\Form\ValidationRules\Rule;

class CheckBoxField extends Field
{
    /** @var McqOption[] */
    private array $options = [];

    public function __construct(
        int $id,
        string $label,
        string $slug,
        int $order,
        private readonly ?Rule $validationRule = null,
        ?string $description = null,
        ?int $groupId = null,
        ?string $resubmissionNote = null,
        ?bool $isResubmissionRequested = null,
        bool $isResubmitted = false,
        ?ConditionWrapper $visibilityCondition = null,
        private ?array $values = null,
        private ?string $cachedKey = null,
        ?array $visibilityDependentField = null
    ) {
        parent::__construct(
            id: $id,
            type: FieldType::CHECKBOX,
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

    public function addOption(McqOption $mcqOption): self
    {
        $this->options[] = $mcqOption;

        return $this;
    }

    /**
     * @param  array<McqOption>  $options
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public static function fromArray(array $array): static
    {
        $rules = Field::getRulesFromArray($array);

        $self = new static(
            id: $array['id'],
            label: $array[Field::LABEL_ARRAY_KEY],
            slug: $array[Field::SLUG_ARRAY_KEY],
            order: $array['order'],
            validationRule: $rules == null ? null : Rule::fromArray($rules),
            description: $array[Field::DESCRIPTION_ARRAY_KEY],
            groupId: $array['group_id'],
            visibilityCondition: isset($array['visible_if']) ? ConditionWrapper::fromArray($array['visible_if']): null,
            values: Field::getValueFromArray($array),
            cachedKey: $array['cached_key'] ?? null,
            visibilityDependentField: $array['visibility_dependent_field'] ?? null
        );

        $possibleValues = $array['possible_values'];
        if ($possibleValues) {
            foreach ($possibleValues as $value) {
                $self->addOption(McqOption::fromJson(json_encode($value)));
            }
        }

        return $self;
    }

    public function getOptionsJson(): string
    {
        return json_encode($this->getOptionsArray());
    }

    public function getOptionsArray(): array
    {
        return array_map(function (McqOption $mcqOption) {
            return $mcqOption->toArray();
        }, $this->options);
    }

    /**
     * @return McqOption[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array<string>|null $values
     * @return $this
     * @throws ValidationRuleException
     */
    public function validateAndSetValue(?array $values, string $label): self
    {
        $this->validationRule?->validateCommonRules($values, $label);

        $values = $values ?? [];

        foreach ($values as $key => $value){
            if ($value === null) {
                unset($values[$key]);
                continue;
            }
            if (!$this->doesMatch($value)){
                throw new ValidationRuleException($this->label . '\'s value does not match any available options');
            }
        }

        return $this->setValue($values);
    }
    public function setValue(?array $values): self
    {
        $this->values = $values;
        return $this;
    }

    public function getValue(): array
    {
        return $this->values;
    }

    private function doesMatch(string $value): bool
    {
        foreach ($this->options as $option) {
            if ($option->doesMatch($value)) {
                return true;
            }
        }

        return false;
    }

    protected function getValidationRule(): ?Rule
    {
        return $this->validationRule;
    }

    public function getPlainValue(): array|string|int|float|null
    {
        return $this->values;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['cached_key'] = $this->cachedKey;
        $data['possible_values'] = $this->getOptionsArray();

        return $data;
    }

    public function matchConditionValue(Condition $condition): bool
    {
        if (($this->values === null || count($this->values) === 0) && count($condition->value) === 0) return true;

        foreach ($this->values as $value)
        {
            if (in_array($value, $condition->value)) return true;
        }
        return false;
    }
}
