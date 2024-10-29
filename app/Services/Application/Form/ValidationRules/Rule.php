<?php

namespace App\Services\Application\Form\ValidationRules;

use App\Helpers\JsonAndArrayAble;
use App\Services\Application\Form\ValidationRuleException;

class Rule extends JsonAndArrayAble
{
    public function __construct(public readonly bool $isRequired = true, public readonly ?string $isRequiredMessage = ' দেওয়া অত্যাবশ্যক')
    {
    }

    public function toArray(): array
    {
        return ['is_required' => ['value' => $this->isRequired, 'message' => $this->isRequiredMessage]];
    }

    public static function fromArray(array $array): static
    {
        return new static(isRequired: $array['is_required']['value'], isRequiredMessage: $array['is_required']['message']);
    }

    /**
     * @throws ValidationRuleException
     */
    public function validateCommonRules(mixed $data, string $label): void
    {
        if ($this->isRequired) {
            $this->checkIsRequired($data, $label);
        }
    }

    /**
     * @throws ValidationRuleException
     */
    public function checkIsRequired($data, string $label)
    {
        if (is_array($data) && (count($data) === 0 || empty(trim($data[0])))) {
            throw new ValidationRuleException($label.'-এর '.$this->isRequiredMessage);
        }else if (!is_array($data)){
            $data = trim($data);
            if ($data === '') {
                throw new ValidationRuleException($label.'-এর '.$this->isRequiredMessage);
            }
        }
    }
}
