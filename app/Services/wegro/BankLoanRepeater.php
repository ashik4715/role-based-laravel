<?php

namespace App\Services\wegro;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Form\Repeat\LabelRepeatGenerator;

class BankLoanRepeater implements LabelRepeatGenerator
{
    public function generate(?ApplicationData $applicationData): array
    {
        if (!$applicationData) throw new \Exception("Application Data cannot be empty");

        return [
            'loan_1' => [
                "key" => "loan_1",
                "label" => "লোনের তথ্য",
                "order" => 1
            ],
            'loan_2' => [
                "key" => "loan_2",
                "label" => "দ্বিতীয় লোনের তথ্য",
                "order" => 2
            ],
            'loan_3' => [
                "key" => "loan_3",
                "label" => "তৃতীয় লোনের তথ্য",
                "order" => 3
            ],
            'loan_4' => [
                "key" => "loan_4",
                "label" => "চতুর্থ লোনের তথ্য",
                "order" => 4
            ],
            'loan_5' => [
                "key" => "loan_5",
                "label" => "পঞ্চম লোনের তথ্য",
                "order" => 4
            ]
        ];
    }
}
