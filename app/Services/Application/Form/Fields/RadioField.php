<?php

namespace App\Services\Application\Form\Fields;

use App\Services\Application\Form\Condition;
use App\Services\Application\Form\ConditionWrapper;
use App\Services\Application\Form\PreloadedData\PreloadedDependencyValidator;
use App\Services\Application\Form\ValidationRuleException;
use App\Services\Application\Form\ValidationRules\Rule;

class RadioField extends Field
{
    /** @var McqOption[] */
    private array $options = [];

    public function __construct(
        int $id,
        FieldType $type,
        string $label,
        string $slug,
        int $order,
        private readonly ?Rule $validationRule = null,
        ?string $description = null,
        ?int $groupId = null,
        bool $isResubmitted = false,
        ?string $resubmissionNote = null,
        ?bool $isResubmissionRequested = null,
        private ?string $value = null,
        ?ConditionWrapper $visibilityCondition = null,
        protected readonly ?string $cachedKey = null,
        ?array $visibilityDependentField = null,
        ?string $dependentField = null
    ) {
        parent::__construct(
            id: $id,
            type: $type,
            label: $label,
            slug: $slug,
            order: $order,
            description: $description,
            groupId: $groupId,
            visibilityCondition: $visibilityCondition,
            resubmissionNote: $resubmissionNote,
            isResubmissionRequested: $isResubmissionRequested,
            isResubmitted: $isResubmitted,
            visibilityDependentField: $visibilityDependentField,
            dependentField: $dependentField
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
            type: Fieldtype::tryFrom($array['type']),
            label: $array[Field::LABEL_ARRAY_KEY],
            slug: $array[Field::SLUG_ARRAY_KEY],
            order: $array['order'],
            validationRule: $rules == null ? null : Rule::fromArray($rules),
            description: $array[Field::DESCRIPTION_ARRAY_KEY],
            groupId: $array['group_id'],
            isResubmitted: $array['is_resubmitted'] ?? false,
            resubmissionNote: $array['resubmission_note'] ?? null,
            isResubmissionRequested: $array['is_resubmission_requested'] ?? null,
            value: Field::getValueFromArray($array),
            visibilityCondition: isset($array['visible_if']) ? ConditionWrapper::fromArray($array['visible_if']): null,
            cachedKey: $array['cached_key'] ?? null,
            visibilityDependentField: $array['visibility_dependent_field'] ?? null,
            dependentField: $array['dependent_field'] ?? null
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
     * @throws ValidationRuleException
     */
    public function validateAndSetValue(?string $value, string $label): self
    {
        $this->validationRule?->validateCommonRules($value, $label);
        if ($value !== null && ! $this->doesMatch($value)) {
            throw new ValidationRuleException($this->label . '\'s value does not match any available options');
        }
        $this->value = $value;

        return $this;
    }

    /**
     * @throws ValidationRuleException
     */
    public function setValue(string $value): self
    {
        $this->validateAndSetValue($value);

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function doesMatch(string $value): bool
    {

        $options = $this->options;
        if ($this->cachedKey) {
            $this->value = $value;

            return PreloadedDependencyValidator::getInstance()->validate($this);
        }

        foreach ($options as $option) {
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
        return $this->value;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['dependent_field'] = $this->dependentField;
        $data['cached_key'] = $this->cachedKey;
        $data['possible_values'] = $this->getOptionsArray();

        return $data;
    }

    public function matchConditionValue(Condition $condition): bool
    {
        return $this->value === $condition->value;
    }

    public function getCachedKey(): ?string
    {
        return $this->cachedKey;
    }
}
