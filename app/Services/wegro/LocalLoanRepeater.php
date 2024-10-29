<?php

namespace App\Services\wegro;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Form\Repeat\LabelRepeatGenerator;

class LocalLoanRepeater implements LabelRepeatGenerator
{
    public function generate(?ApplicationData $applicationData): array
    {
        if (!$applicationData) throw new \Exception("Application Data cannot be empty");

        return [
            'local_loan_1' => [
                "key" => "local_loan_1",
                "label" => "লোনের তথ্য",
                "order" => 1
            ],
            'local_loan_2' => [
                "key" => "local_loan_2",
                "label" => "দ্বিতীয় লোনের তথ্য",
                "order" => 2
            ],
            'local_loan_3' => [
                "key" => "local_loan_3",
                "label" => "তৃতীয় লোনের তথ্য",
                "order" => 3
            ],
            'local_loan_4' => [
                "key" => "local_loan_4",
                "label" => "চতুর্থ লোনের তথ্য",
                "order" => 4
            ],
            'local_loan_5' => [
                "key" => "local_loan_5",
                "label" => "পঞ্চম লোনের তথ্য",
                "order" => 4
            ]
        ];
    }
}
