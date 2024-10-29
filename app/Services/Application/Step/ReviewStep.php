<?php

namespace App\Services\Application\Step;

use App\Models\Application;
use App\Repositories\Application\ApplicationRepositoryInterface;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Form\Condition;

class ReviewStep extends Step
{
    use SimpleApplicationStepAdder, InvalidResubmissionRequestStep;

    /*** @var ApplicationRepositoryInterface */
    private mixed $applicationrepository;

    public function __construct(
        string $label,
        ?string $description,
        string $sectionSlug,
        int $sectionOrder,
        private readonly ?ApplicationData $applicationData = null,
    )
    {
        $this->applicationrepository = app(ApplicationRepositoryInterface::class);
        parent::__construct(
            type: StepType::REVIEW,
            label: $label,
            description: $description,
            sectionSlug: $sectionSlug,
            sectionOrder: $sectionOrder,
        );
    }

    public static function fromArray(array $array): static
    {
        return new static(
            label: $array['label'],
            description: $array['description'],
            sectionSlug: $array['section_slug'],
            sectionOrder: $array['section_order'],
        );
    }

    public function toArray(): array
    {
        return parent::toArray();
    }

    public function toArrayForApi(): array
    {
        $applicationUserInfo = $this->applicationData->getUserInfo();
        $application = $this->applicationrepository->getLatestApplicationByMobile($applicationUserInfo->mobile);
        $status = $application->getStatus();
        $data = parent::toArray();
        $data['show_review'] = false;
        $data['progress'] = $this->applicationData->getProgressPercentage($status);
        $data['user_info'] = $applicationUserInfo->toArray();
        $data['address_info'] = $application->getAddress();
        $data['sections'] = $this->applicationData->toArrayForReview($status);
        $data['application_status'] = $status->getBanglaStatus();
        usort($data['sections'], fn ($a, $b) => $a['section_order'] <=> $b['section_order']);

        return $data;
    }

    public function matchConditionValue(Condition $condition)
    {
        // TODO: Implement matchConditionValue() method.
    }
}
