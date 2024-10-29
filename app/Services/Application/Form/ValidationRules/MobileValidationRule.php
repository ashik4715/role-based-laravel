<?php

namespace App\Services\Application\Form\ValidationRules;

class MobileValidationRule extends Rule
{
    public function toArray(): array
    {
        return [];
    }

    public static function fromArray(array $array): static
    {
        return new static();
    }

    public function validate($value)
    {
    }
}
