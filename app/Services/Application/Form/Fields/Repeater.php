<?php

namespace App\Services\Application\Form\Fields;

use App\Helpers\JsonAndArrayAble;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Form\Repeat\LabelRepeatGenerator;
use App\Services\wegro\SpouseRepeater;

class Repeater extends JsonAndArrayAble
{
    private LabelRepeatGenerator $repeatable;

    public function __construct(
        public readonly string $repeatableText,
        public readonly string $repeatableClass,
        public array $repeatData = [],
    )
    {
        $this->repeatable = app($this->repeatableClass);
    }

    public function generateRepeatData(?ApplicationData $applicationData)
    {
         $this->repeatData = $this->repeatable->generate($applicationData);
    }

    public function getRepeatData(): array
    {
        return $this->repeatData;
    }

    public function setRepeatValue(string $key, mixed $value)
    {
        $this->repeatData[$key]['value'] = $value;
    }

    public function toArray(): array
    {
        return [
            'repeatable_text' => $this->repeatableText,
            'repeatable_class' => $this->repeatableClass,
            'repeat_data' => $this->repeatData
        ];
    }

    public function toArrayForApi(): array
    {
        uasort($this->repeatData, function ($a, $b) {
            return $a['order'] > $b['order'];
        });
        return [
            'repeatable_text' => $this->repeatableText,
            'repeatable_class' => $this->repeatableClass,
            'repeat_data' => array_values($this->repeatData)
        ];
    }

    public static function fromArray(array $array): static
    {
        return new static(
            repeatableText: $array['repeatable_text'],
            repeatableClass: $array['repeatable_class'],
            repeatData: $array['repeat_data'] ?? []
        );
    }
}
