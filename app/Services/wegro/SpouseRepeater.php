<?php

namespace App\Services\wegro;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\ApplicationData\ApplicationDataGenericSection;
use App\Services\Application\Form\Fields\CheckBoxField;
use App\Services\Application\Form\Repeat\LabelRepeatGenerator;
use App\Services\Application\Step\SurveyStep;

class SpouseRepeater implements LabelRepeatGenerator
{
    public function generate(?ApplicationData $applicationData): array
    {
        if (!$applicationData) throw new \Exception("Application Data cannot be empty");

        return [
            'first_spouse_' => [
                "key" => "first_spouse_",
                "label" => "স্বামী/স্ত্রীর তথ্য",
                "order" => 1
            ],
            'second_spouse_' => [
                "key" => "second_spouse_",
                "label" => "দ্বিতীয় স্বামী/স্ত্রীর তথ্য",
                "order" => 2
            ],
            'third_spouse_' => [
                "key" => "third_spouse_",
                "label" => "তৃতীয় স্বামী/স্ত্রীর তথ্য",
                "order" => 3
            ],
            'fourth_spouse_' => [
                "key" => "fourth_spouse_",
                "label" => "চতুর্থ স্বামী/স্ত্রীর তথ্য",
                "order" => 4
            ]
        ];
    }
}
