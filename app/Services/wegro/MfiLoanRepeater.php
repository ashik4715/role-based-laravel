<?php

namespace App\Services\wegro;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Form\Repeat\LabelRepeatGenerator;

class MfiLoanRepeater implements LabelRepeatGenerator
{
    public function generate(?ApplicationData $applicationData): array
    {
        if (!$applicationData) throw new \Exception("Application Data cannot be empty");

        return [
            'mfi_loan_1' => [
                "key" => "mfi_loan_1",
                "label" => "লোনের তথ্য",
                "order" => 1
            ],
            'mfi_loan_2' => [
                "key" => "mfi_loan_2",
                "label" => "দ্বিতীয় লোনের তথ্য",
                "order" => 2
            ],
            'mfi_loan_3' => [
                "key" => "mfi_loan_3",
                "label" => "তৃতীয় লোনের তথ্য",
                "order" => 3
            ],
            'mfi_loan_4' => [
                "key" => "mfi_loan_4",
                "label" => "চতুর্থ লোনের তথ্য",
                "order" => 4
            ],
            'mfi_loan_5' => [
                "key" => "mfi_loan_5",
                "label" => "পঞ্চম লোনের তথ্য",
                "order" => 4
            ]
        ];
    }
}
