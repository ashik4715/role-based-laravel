<?php

namespace App\Services\Application\Form\PreloadedData;

use App\Services\Application\Form\Fields\RadioField;

class PreloadedDependencyValidator
{
    private static ?PreloadedDependencyValidator $instance = null;

    /**
     * @param  array<RadioField>  $dependencyMcqFields
     */
    private function __construct(
        private readonly array $allPreloadedData,
        public ?array $dependencyMcqFields = null
    ) {
    }

    public static function getInstance(): ?PreloadedDependencyValidator
    {
        if (self::$instance === null) {
            self::$instance = new self(
                allPreloadedData: PreloadedData::getData(),
                dependencyMcqFields: []
            );
        }

        return self::$instance;
    }

    public function validate(RadioField $radioField): bool
    {
        $preloadedData = $this->getValidPreloadedData($radioField);
        if ($preloadedData === null) {
            return false;
        }
        if ($radioField->dependentField === null && $this->dependencyMcqFields === null) {
            return true;
        } elseif (count($this->dependencyMcqFields) === 0 && $radioField->dependentField !== null) {
            $this->dependencyMcqFields[] = $radioField;

            return true;
        }
        $validationResult = $this->checkDependency($radioField, $preloadedData);
        $this->dependencyMcqFields = [];
        if ($radioField->dependentField) {
            $this->dependencyMcqFields[] = $radioField;
        }

        return $validationResult;
    }

    private function checkDependency(RadioField $radioField, array $preloadedData): bool
    {
        $dependencyMcqFieldValue = '';
        foreach ($this->dependencyMcqFields as $dependencyMcqField) {
            if ($dependencyMcqField->dependentField === $radioField->slug) {
                $dependencyMcqFieldValue = $dependencyMcqField->getValue();
            }
        }
        if ($dependencyMcqFieldValue === '') {
            return false;
        }
        if ($preloadedData['parent']['value'] !== $dependencyMcqFieldValue) {
            return false;
        }

        return true;
    }

    private function getValidPreloadedData(RadioField $radioField): ?array
    {
        $preloadedData = $this->allPreloadedData[$radioField->getCachedKey()];
        foreach ($preloadedData as $aPreloadedData) {
            if ($aPreloadedData['value'] === $radioField->getValue()) {
                return $aPreloadedData;
            }
        }

        return null;
    }
}
