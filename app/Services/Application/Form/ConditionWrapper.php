<?php

namespace App\Services\Application\Form;

use App\Helpers\JsonAndArrayAble;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Step\Step;

class ConditionWrapper extends JsonAndArrayAble
{
    private ApplicationData $applicationData;

    public function __construct(public readonly ?Condition $backend = null, public readonly ?Condition $frontend = null)
    {
    }

    public static function fromArray(array $array): static
    {
        return new self(
            isset($array['backend']) ? Condition::fromArray($array['backend']) : null,
            isset($array['frontend']) ? Condition::fromArray($array['frontend']) : null
        );
    }

    public function toArray(): array
    {
        return [
            'backend' => $this->backend?->toArray(),
            'frontend' => $this->frontend?->toArray(),
        ];
    }

    public function setApplicationData(ApplicationData $applicationData):self
    {
        $this->applicationData = $applicationData;
        return $this;
    }

    public function isBackendVisible(): bool
    {
        if ($this->backend === null) return true;
        return $this->applicationData->getSection($this->backend->section)->matchConditionValue($this->backend);
    }

    public function isFrontendVisible(Step $step): bool
    {
        if ($this->frontend === null) return true;
        return $step->matchConditionValue($this->frontend);
    }
}
