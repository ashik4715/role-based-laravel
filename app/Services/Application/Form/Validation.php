<?php

namespace App\Services\Application\Form;

use App\Models\Page;
use App\Models\Section;
use App\Services\Application\Form\Fields\Field;

class Validation
{
    public function __construct(private readonly array $data, private readonly string $sectionSlug, private readonly string $pageSlug)
    {
    }

    public function validate()
    {
        $fields = $this->getSectionFields();
        foreach ($fields as $field) {
//            $field->getValidationRule()?->validate($this->data[$field->getSlug()]);
        }
    }

    /**
     * @return Field[]
     */
    private function getSectionFields()
    {
        /** @var Section $section */
        $section = Section::where('slug', $this->sectionSlug)->first();
        $page = Page::where('slug', $this->pageSlug)->where('section_id', $section->id)->first();
        $step = $section->getStep($page);

        return $step->getFields();
    }

    private function getValidationRules($field)
    {
    }
}
