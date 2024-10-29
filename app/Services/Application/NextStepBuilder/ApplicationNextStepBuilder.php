<?php

//
//namespace App\Services\Application\NextStepBuilder;
//
//use App\Models\Application;
//use App\Services\Application\Form\Fields\FieldType;
//use App\Services\Application\Step\FormSectionStep;
//use App\Services\Application\Step\Step;
//use App\Services\Application\Step\StepType;
//use App\Services\Application\Step\SuccessStep;
//
//class ApplicationNextStepBuilder implements NextStepBuilder
//{
//    private mixed $applicationData;
//
//    private $section;
//
//    public function __construct(private readonly Application $application)
//    {
//    }
//
//    public function getNextStep(string $sectionSlug = null, string $pageSlug = null): Step
//    {
//        $this->applicationData = json_decode($this->application->application_data);
//        $this->section = $this->applicationData->{$sectionSlug};
//        $currentPageData = [];
//        $page = null;
//        if ($this->section->type == StepType::NID->value) {
//            $page = $this->section->{$pageSlug};
//            $currentPageData = (object) ['nid_images' => $this->section->nid_images, 'nid_details' => $this->section->nid_details];
//        } elseif ($this->section->type == StepType::TRADE_LICENSE->value) {
//            $page = $this->section->{$pageSlug};
//            $currentPageData = (object) ['trade_license_image' => $this->section->trade_license_image, 'trade_license_information' => $this->section->trade_license_information];
//        } elseif ($this->section->type == StepType::FORM_SECTION->value) {
//            $page = $this->section->pages->{$pageSlug};
//            $currentPageData = $this->applicationData->{$sectionSlug}->pages;
//        }
//
//        return $this->getNextPage($currentPageData, $page);
//    }
//
//    private function getNextPage($current_page_data, $page)
//    {
//        $page_slug = $page ? $page->page_slug : null;
//        $current_page_order = $current_page_data ? $current_page_data->{$page_slug}->page_order : null;
//        foreach ($current_page_data as $page) {
//            if ($page->page_order == $current_page_order + 1) {
//                return $this->buildForm($page, null);
//            }
//        }
//
//        return $this->getNextSection();
//    }
//
//    private function buildForm($page, $section)
//    {
//        $data = $page ?? (property_exists($section, 'pages') ? array_values((array) $section->pages)[0] : $section);
//        if ($data->type == StepType::FORM_SECTION->value) {
//            $fields = array_map(function ($item) {
//                return FieldType::tryFrom($item->type)->getFieldByArray((array) $item);
//            }, array_values((array) $data->values));
//            $step = new FormSectionStep(label: $data->label, description: $data->description, sectionSlug: $data->section_slug, sectionOrder: $data->section_order, pageSlug: $data->page_slug, pageOrder: $data->page_order);
//            foreach ($fields as $field) {
//                $step->addOrUpdateField($field);
//            }
//
//            return $step;
//        } elseif ($data->type === StepType::NID->value) {
//            return StepType::tryFrom($data->type)->getStepByArray((array) $data->nid_images);
//        } elseif ($data->type === StepType::TRADE_LICENSE->value) {
//            return StepType::tryFrom($data->type)->getStepByArray((array) $data->trade_license_image);
//        }
//
//        return StepType::tryFrom($data->type)->getStepByArray((array) $data);
////        if ($data->type == StepType::SURVEY->value) {
////            $possible_values = array_map(function ($item) {
////                return McqOption::fromArray((array)$item);
////            }, $data->values);
////            return new Step\SurveyStep(label: $data->label, description: $data->description, sectionSlug: $data->section_slug, sectionOrder: $data->section_order, possibleValues: $possible_values);
////        }
////        return new Step(type: StepType::tryFrom($data->type), label: $data->label, description: $data->description, sectionSlug: $data->section_slug, sectionOrder: $data->section_order);
//    }
//
//    private function getNextSection()
//    {
//        $section = $this->section;
//        $section_slug = $section->section_slug;
//        $current_section_order = $this->applicationData->$section_slug->section_order;
//        foreach ($this->applicationData as $section) {
//            if (! isset($section->section_order) || $section->section_order > $current_section_order + 1) {
//                continue;
//            }
//            if ($section->section_order == $current_section_order + 1) {
//                return $this->buildForm(null, $section);
//            }
//        }
//
//        return new SuccessStep();
//    }
//}
