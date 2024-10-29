<?php

namespace App\Services\wegro;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\ApplicationData\ApplicationDataGenericSection;
use App\Services\Application\Form\Fields\CheckBoxField;
use App\Services\Application\Form\Repeat\LabelRepeatGenerator;
use App\Services\Application\Step\SurveyStep;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ExperienceRepeater implements LabelRepeatGenerator
{
    public function generate(?ApplicationData $applicationData): array
    {
        if (!$applicationData) throw new \Exception("Application Data cannot be empty");

        return [
            'season_1' => [
                "key" => "season_1",
                "label" => " মৌসুম-১",
                "order" => 1
            ],
            'season_2' => [
                "key" => "season_2",
                "label" => " মৌসুম-২",
                "order" => 2
            ],
            'season_3' => [
                "key" => "season_3",
                "label" => " মৌসুম-৩",
                "order" => 3
            ],
            'season_4' => [
                "key" => "season_4",
                "label" => " মৌসুম-৪",
                "order" => 4
            ]
        ];
    }
}
