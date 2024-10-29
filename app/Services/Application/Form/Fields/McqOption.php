<?php

namespace App\Services\Application\Form\Fields;

use App\Helpers\JsonAndArrayAble;
use App\Services\Application\Condition\InvalidConditionException;
use App\Services\Application\Form\Condition;
use App\Services\Application\Form\Conditions;
use App\Services\Requester\Requester;

class McqOption extends JsonAndArrayAble
{
    public function __construct(
        public readonly string $label,
        public readonly string $value,
        public readonly ?string $description = null,
        public readonly ?string $icon = null,
        public readonly ?array $visibleIf = null
    ) {
    }

    public static function fromArray(array $array): static
    {
        $icon = array_key_exists('icon', $array) ? $array['icon'] : null;
        $visibleIf = array_key_exists('visible_if', $array) && $array['visible_if'] != null ? Conditions::fromArray($array['visible_if']) : null;

        return new self(label: $array['label'], value: $array['value'], description: $array['description'] ?? null, icon: $icon, visibleIf: $visibleIf);
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'description' => $this->description,
            'value' => $this->value,
            'icon' => $this->icon,
            'visible_if' => $this->visibleIf,
//                ?->toArray(),
        ];
    }

    public function toArrayForApi(?string $sectionSlug = null, ?string $pageSlug = null, ?string $lang = null): array
    {
        /*if ($this->visibleIf && $sectionSlug === null) {
            throw new InvalidOptionException('Section slug is required for this option');
        }*/

        $data =  [
            'label' => $this->label,
            'description' => $this->description,
            'value' => $this->value,
            'icon' => $this->icon,
            'visible_if' => null,
        ];

        if ($this->visibleIf) {
            $data['visible_if'] = $this->visibleIf;
        }

        return $data;
    }

    public function doesMatch(string $data): bool
    {
        return $this->value == $data;
    }
}
