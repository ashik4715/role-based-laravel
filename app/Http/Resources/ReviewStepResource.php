<?php

namespace App\Http\Resources;

use App\Models\Application;
use App\Services\Application\Step\ReviewStep;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewStepResource extends JsonResource
{
    public function __construct(
        private readonly ReviewStep $step,
        private readonly Application $application
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
        return $this->step->toArrayForApi($this->application);
    }
}
