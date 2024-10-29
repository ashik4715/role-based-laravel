<?php

namespace App\Services\Application\Form\ValidationRules;

use App\Helpers\NumberRange;
use App\Services\Application\Form\ValidationRuleException;

class NumberValidationRule extends Rule
{
    private ?NumberRange $range = null;
    private ?float $minValue = null;
    private ?string $minValueMessage = null;

    /**
     * Constructor to set min value and custom message.
     *
     * @param float|null $minValue
     * @param string|null $minValueMessage
     */
    public function __construct(?float $minValue = 50000, ?string $minValueMessage = 'মাসিক আয় ৫০,০০০ এর কম হতে পারবে না')
    {
        $this->minValue = $minValue;
        $this->minValueMessage = $minValueMessage;
    }

    /**
     * Convert the validation rule to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = $this->range?->toArray() ?? [];
        $data['min_value'] = $this->minValue;
        $data['min_value_message'] = $this->minValueMessage;
        return $data;
    }

    /**
     * Static method to create an instance from an array.
     *
     * @param array $array
     * @return static
     */
    public static function fromArray(array $array): static
    {
        $self = new self(
            minValue: $array['min_value'] ?? 50000,
            minValueMessage: $array['min_value_message'] ?? 'মাসিক আয় ৫০,০০০ এর কম হতে পারবে না'
        );

        if (!empty($array['range'])) {
            $self->setRange(NumberRange::fromArray($array['range']));
        }

        return $self;
    }

    /**
     * Validate the provided data against the min value and range.
     *
     * @param int|float $data
     * @param string $label
     * @throws ValidationRuleException
     */
    public function validate(int|float $data, string $label): void
    {
        if ($this->minValue !== null && $data < $this->minValue) {
            throw new ValidationRuleException($label . ' ' . $this->minValueMessage);
        }

        if ($this->range !== null && $this->range->isNotInRange($data)) {
            throw new ValidationRuleException($label . ' ' . 'The number should be within ' . $this->range->toString());
        }
    }

    /**
     * Set the number range for validation.
     *
     * @param NumberRange|null $range
     * @return NumberValidationRule
     */
    public function setRange(?NumberRange $range): NumberValidationRule
    {
        $this->range = $range;
        return $this;
    }

    /**
     * Convert the validation rule to JSON.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
