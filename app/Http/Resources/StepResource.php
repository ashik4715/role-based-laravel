<?php

namespace App\Http\Resources;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Step\FormSectionStep;
use App\Services\Application\Step\OtpStep;
use App\Services\Application\Step\ReviewStep;
use App\Services\Application\Step\Step;
use App\Services\wegro\BasicCheckerForWegro;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class StepResource extends JsonResource
{
    public function __construct(
        private readonly Step $step,
        private readonly ?ApplicationData $applicationData = null
    )
    {
        parent::__construct($step);
    }

    /**
     * @param $request
     * @return array|\JsonSerializable|Arrayable
     */
    public function toArray($request): array|\JsonSerializable|Arrayable
    {
        if ($this->step instanceof FormSectionStep || $this->step instanceof OtpStep || $this->step instanceof ReviewStep){
            $data = $this->step->toArrayForApi();
        }else {
            $data = $this->step->toArray();
        }
        if ($data['section_slug'] !== 'success') {
            $data['step'] = BasicCheckerForWegro::getStepCounter($this->step->sectionSlug, $this->applicationData);
        }
        return $data;
    }
}
