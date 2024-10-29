<?php

namespace App\Http\Requests;

use App\Models\Page;
use App\Repositories\Application\ApplicationRepositoryInterface;
use App\Repositories\Section\SectionRepositoryInterface;
use App\Rules\BdMobileNumber;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\DTO\ApplicationStoreDto;
use App\Services\Application\DTO\BdMobile;
use App\Services\Application\DTO\SelfieImagesDTO;
use App\Services\Application\DTO\NidDTO;
use App\Services\Application\DTO\SignatureDTO;
use App\Services\Application\Step\FormSectionStep;
use App\Services\Application\Step\NidStep;
use App\Services\Application\Step\OtpStep;
use App\Services\Application\Step\Selfie;
use App\Services\Application\Step\SignatureStep;
use App\Services\Application\Step\Step;
use App\Services\Application\Step\StepType;
use App\Services\Application\Step\SurveyStep;
use App\Services\Requestor\JWTRequest;
use Illuminate\Foundation\Http\FormRequest;

class ApplicationSubmitRequest extends FormRequest
{
    //use SectionPageChecker;

    private SectionRepositoryInterface $sectionRepository;
    private ApplicationRepositoryInterface $applicationRepository;
    private ?ApplicationData $applicationData;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->sectionRepository = app(SectionRepositoryInterface::class);
        $this->applicationRepository = app(ApplicationRepositoryInterface::class);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];
        if ($this->section_slug === StepType::NID->value) {
            if ($this->page_slug === 'nid_images') {
                $rules = [
                    'data.front_image' => ['required', 'image'],
                    'data.back_image' => ['required', 'image'],
                ];
            } elseif ($this->page_slug === 'selfie_images') {
                $rules = [
                    'data.user_image' => ['required', 'image'],
                ];
            }
        } elseif ($this->section_slug === StepType::TRADE_LICENSE->value && $this->page_slug === 'trade_license_image') {
            $rules = ['data.image' => ['required', 'image']];
        } elseif ($this->section_slug === StepType::SIGNATURE->value) {
            $rules = ['data.image' => ['required', 'image']];
        }

        if (! empty($this->mobile)) {
            $rules['mobile'] = ['required', new BdMobileNumber()];
            $rules['section_slug'] = ['required', 'string'];
        }

        return $rules;
    }

    /**
     * @throws \Exception
     */
    public function getData(): ApplicationStoreDto
    {
        return new ApplicationStoreDto(
            mobile: $this->getMobile(),
            requesterType: JWTRequest::getRequesterType(),
            sectionSlug: $this->section_slug,
            pageSlug: $this->page_slug,
            step: $this->getStep(),
            agentId: JWTRequest::getAgentId(),
            lat: $this->isValid($this->header('lat')) ? floatval($this->header('lat')) : null,
            lon: $this->isValid($this->header('lon')) ? floatval($this->header('lon')) : null
        );
    }

    private function isValid($value): bool
    {
        return $value !== null && $value !== '' && $value !== 'null';
    }

    /**
     * @throws \Exception
     */
    public function getMobile(): BdMobile
    {
        return new BdMobile($this->mobile);
    }

    /**
     * @throws \Exception
     */
    public function getStep(): Step
    {
        $step = $this->getSectionStep();
        if ($step instanceof OtpStep) {
            return $step->setValue(new BdMobile($this->mobile));
        }
        if ($step instanceof NidStep) {
            return $step->setValue(NidDTO::uploadNidImages($this->data));
        }
        if ($step instanceof Selfie) {
            return $step->setValue(SelfieImagesDTO::uploadNidImages($this->data));
        }

        if ($step instanceof SignatureStep) {
            return $step->setValue(SignatureDTO::uploadSignature($this->data));
        }
        if ($step instanceof SurveyStep) {
            if (!$this->data) throw new \Exception("চাষের ধরণ নির্বাচন করুন");
            $step->field->validateAndSetValue($this->data['value'], $step->field->label);
            return $step;
        }
        if ($step instanceof FormSectionStep) {
            foreach ($step->getFields() as $field) {
                if ($field->visibilityCondition === null || $field->visibilityCondition?->isFrontendVisible($step)) {
                    if ($field->repeater) {
                        foreach ($field->repeater->getRepeatData() as $datum) {
                            $slug = $datum['key'];
                            if (!empty($this->data[$slug])) {
                                $field->setRepeatableValue($datum['key'], $this->data[$slug]);
                            }
                        }
                    } else {
                        $field->prepareAndSetValue($this->data[$field->slug] ?? null);
                    }
                }
            }
            return $step;
        }

        return $step;
    }

    private function getSectionStep(): Step
    {
        $this->applicationData = $this->applicationRepository->getLatestApplicationByMobile(new BdMobile($this->mobile))?->getApplicationData();
        $section = $this->sectionRepository->getSectionBySlug($this->section_slug);
        $page = Page::where('section_id', $section->id)->where('slug', $this->page_slug)->first();

        $step = $this->applicationData?->getStep($this->section_slug, $this->page_slug);
        return $step ?? $section->getStep($page, $this->applicationData);
    }
}
//Section Slug type and data
//Send type to validation class
//Validation Rule from database from validation class(Field Validation Rule)
