<?php

namespace App\Services\Application\Form;

use App\Helpers\JsonAndArrayAble;

class Condition extends JsonAndArrayAble
{
    public function __construct(
        public readonly string $section,
        public readonly mixed $value,
        public readonly ?string $field = null,
        public readonly ?string $page = null,
        public readonly ?string $groupField = null,
    ) {
    }

    public static function fromArray(array $array): static
    {
        $page = array_key_exists('page', $array) ? $array['page'] : null;

        return new self(section: $array['section'], value: $array['value'], field: $array['field'], page: $page, groupField: $array['group_field']);
    }

    public function toArray(): array
    {
        return [
            'section' => $this->section,
            'page' => $this->page,
            'field' => $this->field,
            'value' => $this->value,
            'group_field' => $this->groupField
        ];
    }
}
