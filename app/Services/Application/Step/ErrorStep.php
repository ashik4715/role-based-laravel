<?php

namespace App\Services\Application\Step;

use App\Services\Application\Form\Button\Button;

class ErrorStep extends Step
{
    use SimpleApplicationStepAdder, InvalidResubmissionRequestStep;

    /**
     * @param  Button[]  $buttons
     */
    public function __construct(?string $label, ?string $description, public readonly array $buttons)
    {
        parent::__construct(type: StepType::ERROR, label: $label, description: $description);
    }

    public function toArray(): array
    {
        return [
            'type' => StepType::ERROR,
            'label' => $this->label,
            'description' => $this->description,
            'buttons' => $this->buttons,
        ];
    }
}
