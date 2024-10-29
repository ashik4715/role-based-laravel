<?php

namespace App\Services\Application\Form\Fields;

use App\Services\Application\Form\Condition;
use App\Services\Application\Form\ConditionWrapper;
use App\Services\Application\Form\ValidationRules\Rule;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileField extends Field
{
    public function __construct(
        int                    $id,
        string                 $label,
        string                 $slug,
        int                    $order,
        ?string                $description = null,
        ?int                   $groupId = null,
        private readonly ?Rule $validationRule = null,
        ?string                $resubmissionNote = null,
        ?bool                  $isResubmissionRequested = null,
        ?ConditionWrapper      $visibilityCondition = null,
        private ?string        $value = null,
        bool                   $isResubmitted = false,
    )
    {
        parent::__construct(
            id: $id,
            type: FieldType::FILE,
            label: $label,
            slug: $slug,
            order: $order,
            description: $description,
            groupId: $groupId,
            visibilityCondition: $visibilityCondition,
            resubmissionNote: $resubmissionNote,
            isResubmissionRequested: $isResubmissionRequested,
            isResubmitted: $isResubmitted
        );
    }

    public static function fromArray(array $array): static
    {
        $rules = Field::getRulesFromArray($array);
        return new static(
            id: $array['id'],
            label: $array['label'],
            slug: $array['slug'],
            order: $array['order'],
            description: $array['description'],
            groupId: $array['group_id'],
            validationRule: $rules == null ? null : Rule::fromArray($rules),
            resubmissionNote: $array['resubmission_note'] ?? null,
            isResubmissionRequested: $array['is_resubmission_requested'] ?? null,
            visibilityCondition: isset($array['visible_if']) ? ConditionWrapper::fromArray($array['visible_if']) : null,
            value: parent::getValueFromArray($array),
            isResubmitted: $array['is_resubmitted'] ?? false
        );
    }

    public function validateAndSetValue(?UploadedFile $file, string $label)
    {
        if (!$file) {
            return;
        }
        $filePath = $file->store($this->slug);
        $this->value = $filePath;
    }

    public function getValue(): ?string
    {
        if ($this->value) {
            return Storage::url($this->value);
        }

        return null;
    }

    public function getPlainValue(): array|string|int|float|null
    {
        return $this->value;
    }

    public function toArrayForApi(): array
    {
        $data = $this->toArray();
        if ($this->value) {
            $data[self::VALUE_ARRAY_KEY] = Storage::url($this->value);
        }
        return $data;
    }

    /**
     * @throws Exception
     */
    public function matchConditionValue(Condition $condition): bool
    {
        throw new Exception('Nothing to match for File type.');
    }

    /**
     * {@inheritDoc}
     */
    protected function getValidationRule(): ?Rule
    {
        return $this->validationRule;
    }
}
