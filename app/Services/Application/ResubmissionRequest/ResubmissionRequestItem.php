<?php

namespace App\Services\Application\ResubmissionRequest;

use App\Services\Application\Form\Fields\Field;
use App\Services\Application\Form\Fields\FieldType;
use App\Services\Application\Form\Fields\RadioField;
use App\Services\Application\Form\Fields\McqOption;

class ResubmissionRequestItem
{
    public function __construct(
        public readonly string $sectionSlug,
        public readonly string $resubmissionNote,
        public readonly ?string $fieldSlug = null,
        public readonly ?string $pageSlug = null,
    ) {}
}
