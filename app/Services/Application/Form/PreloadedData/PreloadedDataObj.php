<?php

namespace App\Services\Application\Form\PreloadedData;

use App\Helpers\JsonAndArrayAble;

class PreloadedDataObj extends JsonAndArrayAble
{
    public function __construct(
        public readonly string $value,
        public readonly ?PreloadedDataObj $parent
    ) {
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'parent' => $this->parent?->toArray(),
        ];
    }

    public static function fromArray(array $array): static
    {
        $parent = array_key_exists('parent', $array) && $array['parent'] != null ? PreloadedDataObj::fromArray($array['parent']) : null;

        return new self(value: $array['value'], parent: $parent);
    }
}
