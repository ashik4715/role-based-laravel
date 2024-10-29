<?php

namespace App\Services\Application\Form\Fields;

use App\Services\Application\Form\Condition;
use App\Services\Application\Form\ConditionWrapper;
use App\Services\Application\Form\ValidationRules\Rule;
use App\Services\Application\Status;
use Exception;

class GroupField extends Field
{
    /**
     * @param array<Field> $children
     */
    public function __construct(
        int               $id,
        string            $label,
        string            $slug,
        int               $order,
        ?string           $description = null,
        ?string           $resubmissionNote = null,
        ?bool              $isResubmissionRequested = null,
        ?ConditionWrapper $visibilityCondition = null,
        private array     $children = [],
        bool              $isRepeatable = false,
        ?Repeater         $repeater = null,
        bool $isResubmitted = false,
        ?array $visibilityDependentField = null
    )
    {
        parent::__construct(
            id: $id,
            type: FieldType::GROUP,
            label: $label,
            slug: $slug,
            order: $order,
            description: $description,
            visibilityCondition: $visibilityCondition,
            resubmissionNote: $resubmissionNote,
            isResubmissionRequested: $isResubmissionRequested,
            isRepeatable: $isRepeatable,
            isResubmitted: $isResubmitted,
            repeater: $repeater,
            visibilityDependentField: $visibilityDependentField
        );
    }

    public static function fromArray(array $array): static
    {
        $children = [];
        if (isset($array['children']) && count($array['children']) > 0) {
            foreach ($array['children'] as $key => $child) {
                $children[$key] = FieldType::tryFrom($child['type'])->getFieldByArray($child);
            }
        }
        return new static(
            id: $array['id'],
            label: $array['label'],
            slug: $array['slug'],
            order: $array['order'],
            description: $array['description'],
            resubmissionNote: $array['resubmission_note'] ?? null,
            isResubmissionRequested: $array['is_resubmission_requested'] ?? null,
            visibilityCondition: isset($array['visible_if']) ? ConditionWrapper::fromArray($array['visible_if']) : null,
            children: $children,
            isRepeatable: $array['is_repeatable'],
            repeater: parent::getRepeater($array),
            isResubmitted: $array['is_resubmitted'] ?? false,
            visibilityDependentField: $array['visibility_dependent_field'] ?? null
        );
    }

    /**
     * @throws Exception
     */
    public function validateAndSetValue($value, string $label)
    {
        uasort($this->children, function ($a, $b) {
            return $a->order > $b->order;
        });
        foreach ($this->children as $field) {
            $field->prepareAndSetValue($value[$field->slug] ?? null);
        }
    }

    /**
     * @throws Exception
     */
    public function setRepeatableValue(string $key, $data)
    {
        foreach ($this->children as $field) {
            if ($field instanceof FileField && $field->getPlainValue()) $data[$field->slug] = [$field->getValue()];
            else $field->prepareAndSetValue($data[$field->slug]);
        }
        $this->repeater->setRepeatValue($key, $data);
    }

    public function getPlainValue(): array|string|int|float|null
    {
        return null;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        foreach ($this->children as $child) {
            $data['children'][$child->slug] = $child->toArray();
        }
        return $data;
    }

    public function toArrayForReview(Status $status): array
    {
        $data = parent::toArrayForApi();
        $data['children'] = [];
        uasort($this->children, function ($a, $b) {
            return $a->order > $b->order;
        });
        foreach ($this->children as $child) {
            $data['children'][] = $child->toArrayForReview($status);
        }
        if ($this->slug == 'previous_experience') $data['update_index'] = false;
        else if ($this->isRepeatable) $data['update_index'] = true;
        return $data;
    }

    public function toArrayForApi(): array
    {
        $data = parent::toArrayForApi();
        $data['children'] = [];
        uasort($this->children, function ($a, $b) {
            return $a->order > $b->order;
        });
        foreach ($this->children as $child) {
            $data['children'][] = $child->toArrayForApi();
        }

        if ($this->slug == 'spouse') $data['update_index'] = true;
        else if ($this->isRepeatable) $data['update_index'] = false;

        return $data;
    }

    public function addChildField(Field $field)
    {
        $this->children[$field->slug] = $field;
    }

    /**
     * @return Field[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getChild(string $fieldSlug): ?Field
    {
        return $this->children[$fieldSlug] ?? null;
    }

    public function hasChild(string $fieldSlug): bool
    {
        return isset($this->children[$fieldSlug]);
    }

    public function matchConditionValue(Condition $condition): bool
    {
        // Need to work for group field
    }

    protected function getValidationRule(): ?Rule
    {
        return null;
    }
}
