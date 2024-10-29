<?php

namespace App\Services\Application;

use App\Models\Application;
use App\Models\Page;
use App\Repositories\Application\ApplicationRepositoryInterface;
use App\Repositories\ApplicationLog\ApplicationLogRepositoryInterface;
use App\Repositories\Section\SectionRepositoryInterface;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\DTO\BdMobile;
use App\Services\Application\Exceptions\ApplicationNotUpdateableException;
use App\Services\Application\Form\Fields\GroupField;
use App\Services\Application\NextStepBuilder\ConfigNextStepBuilder;
use App\Services\Application\Step\FormSectionStep;
use App\Services\Application\Step\ReviewStep;
use App\Services\Application\Step\Step;
use App\Services\Location\BarikoiClient;
use App\Services\Location\Geo;
use App\Services\Requestor\JWTRequest;
use Throwable;

class StoringApplication
{
    private $section;

    private $page;

    private $data;

    private BdMobile $mobile;

    private Step $step;

    private ApplicationRepositoryInterface $applicationRepository;

    private SectionRepositoryInterface $sectionRepository;

    private DraftChecker $draftChecker;

    private ConfigNextStepBuilder $nextStepBuilder;

    private RequesterType $requesterType;

    private ?int $agentId;

    private ApplicationLogRepositoryInterface $applicationLogRepository;

    private Geo $geo;

    public function __construct(ApplicationRepositoryInterface $application_repository, SectionRepositoryInterface $section_repository, DraftChecker $draft_checker, ConfigNextStepBuilder $nextStepBuilder, ApplicationLogRepositoryInterface $applicationLogRepository)
    {
        $this->applicationRepository = $application_repository;
        $this->sectionRepository = $section_repository;
        $this->draftChecker = $draft_checker;
        $this->nextStepBuilder = $nextStepBuilder;
        $this->applicationLogRepository = $applicationLogRepository;
    }

    public function setMobile(BdMobile $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function setSection($section): self
    {
        $this->section = $section;

        return $this;
    }

    public function setPage($page): self
    {
        $this->page = $page;

        return $this;
    }

    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    public function setStep(Step $step): self
    {
        $this->step = $step;

        return $this;
    }

    public function setRequesterType(RequesterType $requesterType): self
    {
        $this->requesterType = $requesterType;

        return $this;
    }

    public function setAgentId(?int $agentId): self
    {
        $this->agentId = $agentId;

        return $this;
    }

    public function setGeo(Geo $geo)
    {
        $this->geo = $geo;

        return $this;
    }

    /**
     * @throws ApplicationNotUpdateableException
     */
    public function store(): Application
    {
        $existingApplication = $this->applicationRepository->getLatestApplicationByMobile($this->mobile);
        if (! $existingApplication) {
            return $this->createNewApplication($this->step);
        }

        if (! $existingApplication->isUpdateable()) {
            throw new ApplicationNotUpdateableException('আবেদনটি প্রক্রিয়াধীন আছে, আপডেট করা যাবেনা');
        }

        $status = $existingApplication->status;
        $applicationData = $existingApplication->getApplicationData();
        $previousApplicationData = $existingApplication->replicate()->getApplicationData();

        $this->step->addStepToApplicationData($applicationData);
        $logData = [];
        if ($status == Status::RESUBMISSION_REQUESTED) {
            $previousData = $previousApplicationData->getSection($this->section);

            $previousStep = $previousData->getStep($this->page);
            $previousFields = null;
            $newDataSection = $applicationData->getSection($this->section);
            $newDataSection->isReSubmissionRequested = null;
            $newDataSection->isResubmitted = true;
            $newDataSectionStep = $newDataSection->getStep($this->page);
            $newDataSectionStep->isReSubmissionRequested = null;
            $newDataSectionStep->isResubmitted = true;
            if ($previousStep instanceof FormSectionStep) {
                $previousFields = $previousStep->getFields();
            }

            foreach ($previousFields as $previousField) {
                $newField = null;
                if ($newDataSectionStep instanceof FormSectionStep) {
                    $newField = $newDataSectionStep->getField($previousField->slug);
                }

                if (! ($previousField instanceof GroupField) && $previousField->getPlainValue() != $newField->getPlainValue()) {
                    $newField->isResubmissionRequested = null;
                    //$newField->resubmissionNote = null;
                    $newField->isResubmitted = true;
                } elseif ($previousField instanceof GroupField && ! $previousField->isRepeatable()) {
                    foreach ($previousField->getChildren() as $child) {
                        $newFieldChild = null;
                        if ($newField instanceof GroupField) {
                            $newFieldChild = $newField->getChild($child->slug);
                        }
                        if ($child->getPlainValue() != $newFieldChild->getPlainValue()) {
                            $newField->isResubmissionRequested = null;
                            $newField->isResubmitted = true;
                            $newFieldChild->isResubmitted = true;
                            $newFieldChild->isResubmissionRequested = null;
                        }
                    }
                } else {
                    $newField->isResubmitted = false;
                }
            }

            $newDataSection = $applicationData->getSection($this->section);
            $newDataSectionFullStep = $newDataSection;
            $newDataSection->isReSubmissionRequested = null;
            $newDataSection = $newDataSection->getStep($this->page);
            $newDataSection->isReSubmissionRequested = null;
            $logData = [
                'application_id' => $existingApplication->id,
                'type' => 'application_data',
                'section_slug' => $this->section,
                'from' => $previousData->toJson(),
                'to' => $newDataSectionFullStep->toJson(),
                'status' => Status::RESUBMITTED,
                'created_by_id' => JWTRequest::getAgentId(),
                'user_type' => 'agent',
            ];
        }

        if ($this->step instanceof FormSectionStep && $status == Status::INITIATED) {
            $status = Status::DRAFTED;
        }

        if ($this->step instanceof ReviewStep) {
            if ($status == Status::RESUBMISSION_REQUESTED) {
                $data['first_submission_at'] = Carbon::now();
            }
            $status = Status::SUBMITTED;
        }

        $data = [
            'application_data' => $applicationData->toJson(),
            'status' => $status,
        ];

        if ($this->requesterType === RequesterType::AGENT) {
            $data['agent_id'] = $this->agentId;
        }

        $existingApplication->update($data);
        if ($status == Status::RESUBMISSION_REQUESTED) {
            $this->applicationLogRepository->create($logData);
        }

        return $existingApplication;
    }

    private function shouldDraft(?Application $existingApplication): bool
    {
        if ($this->isAlreadyDrafted($existingApplication)) {
            return false;
        }

        return (! $existingApplication || $existingApplication->status == Status::INITIATED) && $this->draftChecker->shouldStartDrafting($this->section);
    }

    private function isAlreadyDrafted(?Application $existing_application): bool
    {
        return $existing_application && $existing_application->status !== Status::INITIATED;
    }

    private function createNewApplication(Step $currentSectionStep): Application
    {
        $address = $this->getAddress();
        $status = Status::INITIATED;
        $applicationData = (new ApplicationData());

        if ($this->shouldDraft(null)) {
            $status = Status::DRAFTED;
//            $this->buildAllSection($applicationData);
        }

        $currentSectionStep->addStepToApplicationData($applicationData);

        $data = [
            Application::MOBILE => $this->mobile->getFullNumber(),
            'status' => $status,
            'application_data' => $applicationData->toJson(),
            'address' => $address != null || $address != '' ? json_encode([
                'lat' => $this->geo->getLat(),
                'lon' => $this->geo->getLng(),
                'address' => $address,
            ]) : null,
        ];
        if ($this->requesterType === RequesterType::AGENT) {
            $data['agent_id'] = $this->agentId;
        }

        return $this->applicationRepository->create($data);
    }

//    private function buildAllSection(ApplicationData $applicationData): void
//    {
//        $nextSteps = $this->nextStepBuilder->getAllNextSteps($this->section, $this->page);
//        foreach ($nextSteps as $nextStep) {
//            $nextStep->addStepToApplicationData($applicationData);
//        }
//    }

//    private function addStepToApplicationData(ApplicationData $applicationData, Step $step): void
//    {
//        if ($step instanceof FormSectionStep) {
//            $section = $applicationData->getSection($step->getSectionSlug()) ?? (new ApplicationDataFormSection());
//            $section->addOrUpdatePage($step);
//            $applicationData->addOrUpdateSection($section);
//        } else {
//            $applicationData->addOrUpdateSection(new ApplicationDataGenericSection(step: $step));
//        }
//    }

    private function getSectionStep(): Step
    {
        $section_model = $this->sectionRepository->getSectionBySlug($this->section);
        $page_model = Page::where('section_id', $section_model->id)->where('slug', $this->page)->first();

        return $section_model->getStep($page_model);
    }

    public function getAddress()
    {
        try {
            return (new BarikoiClient)->getAddressFromGeo($this->geo)->getAddress();
        } catch (Throwable $exception) {
            return '';
        }
    }
}
