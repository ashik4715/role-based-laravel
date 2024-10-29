<?php

namespace App\Services\wegro;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Form\Repeat\LabelRepeatGenerator;

class OtherLoanRepeater implements LabelRepeatGenerator
{
    public function generate(?ApplicationData $applicationData): array
    {
        if (!$applicationData) throw new \Exception("Application Data cannot be empty");

        return [
            'other_loan_1' => [
                "key" => "other_loan_1",
                "label" => "লোনের তথ্য",
                "order" => 1
            ],
            'other_loan_2' => [
                "key" => "other_loan_2",
                "label" => "দ্বিতীয় লোনের তথ্য",
                "order" => 2
            ],
            'other_loan_3' => [
                "key" => "other_loan_3",
                "label" => "তৃতীয় লোনের তথ্য",
                "order" => 3
            ],
            'other_loan_4' => [
                "key" => "other_loan_4",
                "label" => "চতুর্থ লোনের তথ্য",
                "order" => 4
            ],
            'other_loan_5' => [
                "key" => "other_loan_5",
                "label" => "পঞ্চম লোনের তথ্য",
                "order" => 4
            ]
        ];
    }
}
