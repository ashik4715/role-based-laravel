<?php

namespace App\Services\Application\Form\Fields;

use App\Helpers\JsonAndArrayAble;
use App\Services\Application\DTO\BdMobile;
use App\Services\Application\Form\Condition;
use App\Services\Application\Form\ConditionWrapper;
use App\Services\Application\Form\ValidationRules\Rule;
use App\Services\Application\ResubmissionRequest\ResubmissionRequestItem;
use App\Services\Application\Status;
use App\Services\InvalidMobileNumberException;
use Carbon\Carbon;
use Exception;

abstract class Field extends JsonAndArrayAble
{
    const LABEL_ARRAY_KEY = 'label';

    const SLUG_ARRAY_KEY = 'slug';

    const DESCRIPTION_ARRAY_KEY = 'description';

    const VALIDATION_RULE_ARRAY_KEY = 'rules';

    const VALUE_ARRAY_KEY = 'value';

    public function __construct(
        public readonly int               $id,
        public readonly FieldType         $type,
        public readonly string            $label,
        public readonly string            $slug,
        public readonly int               $order,
        public readonly ?string           $description = null,
        public readonly ?int              $groupId = null,
        public readonly ?ConditionWrapper $visibilityCondition = null,
        public ?string                    $resubmissionNote = null,
        public ?bool                      $isResubmissionRequested = null,
        public readonly bool              $isRepeatable = false,
        public bool                       $isResubmitted = false,
        public readonly ?Repeater         $repeater = null,
        public readonly ?array            $visibilityDependentField = null,
        public readonly ?string           $dependentField = null
    )
    {
    }

    public static function getRepeater(array $array): ?Repeater
    {
        if (!$array['is_repeatable']) return null;
        return Repeater::fromArray($array['repeatable'] ?? $array);
    }

    public static function getValueFromArray(array $data): array|string|int|float|null
    {
        return array_key_exists(Field::VALUE_ARRAY_KEY, $data) ? $data[Field::VALUE_ARRAY_KEY] : null;
    }

    public static function getRulesFromArray(array $data): array|null
    {
        $rules = array_key_exists(Field::VALIDATION_RULE_ARRAY_KEY, $data) ? $data[Field::VALIDATION_RULE_ARRAY_KEY] : null;
        if ($rules == null) {
            return null;
        }

        return json_decode(json_encode($rules), 1);
    }

    abstract public function matchConditionValue(Condition $condition);

    public function toArrayForReview(Status $status): array
    {
        $data = $this->toArrayForApi();
        if ($data['type'] == 'file' && $data['value']) $data['rules'] = null;
        $isResubmitRequested = $data['is_resubmission_requested'];

        if ($status == Status::RESUBMISSION_REQUESTED) {
            $isResubmitRequested = ($isResubmitRequested !== null) ? $isResubmitRequested : false;
        } elseif ($isResubmitRequested === null) {
            $isResubmitRequested = null;
        }
        if ($status == Status::SUBMITTED || $status == Status::APPROVED || $status == Status::REJECTED || $status == Status::RESUBMITTED) $isResubmitRequested = false;


        $data['is_resubmission_requested'] = $isResubmitRequested;
        $data['is_repeatable'] = $this->isRepeatable;
        $data['repeatable'] = $this->repeater?->toArrayForApi();

        return $data;
    }

    public function toArrayForApi(): array
    {
        $data = $this->toArray();
        $data['is_repeatable'] = $this->isRepeatable;
        $data['repeatable'] = $this->repeater?->toArrayForApi();

        return $data;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            self::LABEL_ARRAY_KEY => $this->label,
            self::DESCRIPTION_ARRAY_KEY => $this->description,
            self::SLUG_ARRAY_KEY => $this->slug,
            'order' => $this->order,
            'type' => $this->type->value,
            self::VALIDATION_RULE_ARRAY_KEY => $this->getValidationRule()?->toArray(),
            'group_id' => $this->groupId,
            'resubmission_note' => $this->resubmissionNote,
            'is_resubmission_requested' => $this->isResubmissionRequested,
            'is_resubmitted' => $this->isResubmitted,
            self::VALUE_ARRAY_KEY => $this->getPlainValue(),
            'visible_if' => $this->visibilityCondition?->toArray(),
            'is_repeatable' => $this->isRepeatable,
            'repeatable' => $this->repeater?->toArray(),
            'visibility_dependent_field' => $this->visibilityDependentField,
            'dependent_field' => $this->dependentField
        ];
    }

    /**
     * @return Rule|null
     */
    abstract protected function getValidationRule(): ?Rule;

    abstract public function getPlainValue(): array|string|int|float|null;

    public function requestForResubmission(ResubmissionRequestItem $item): self
    {
        $this->resubmissionNote = $item->resubmissionNote;
        $this->isResubmissionRequested = true;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function prepareAndSetValue(mixed $value)
    {
        if ($this->type === FieldType::DATE) {
            try {
                $value = Carbon::createFromFormat('Y-m-d', $value);
            } catch (Exception $e) {
                throw new Exception($this->label . " দেওয়া অত্যাবশ্যক", 400);
            }
            $this->validateAndSetValue($value, $this->label);
        } elseif ($this->type === FieldType::MOBILE) {
            try {
                $bdMobile = $value ? new BdMobile($value) : null;
            } catch (InvalidMobileNumberException $e) {
                throw new InvalidMobileNumberException($this->label . " সঠিক নয়");
            }
            $this->validateAndSetValue($bdMobile, $this->label);
        } elseif ($this->type === FieldType::FILE) {
            $this->validateAndSetValue($value, $this->label);
        } else {
            $this->validateAndSetValue($value, $this->label);
        }
    }

    public function isRepeatable(): bool
    {
        return !is_null($this->repeater);
    }
}
