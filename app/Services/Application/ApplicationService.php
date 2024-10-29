<?php

namespace App\Services\Application;

use App\Models\Application;
use App\Repositories\Application\ApplicationRepository;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\ApplicationData\ApplicationDataFormSection;
use App\Services\Application\ApplicationData\ApplicationDataGenericSection;
use App\Services\Application\ApplicationData\ApplicationDataNidSection;
use App\Services\Application\DTO\ApplicationStoreDto;
use App\Services\Application\Exceptions\ApplicationNotUpdateableException;
use App\Services\Application\Exceptions\InvalidSectionDataException;
use App\Services\Application\NextStepBuilder\ConfigNextStepBuilder;
use App\Services\Application\Step\InformationStep;
use App\Services\Application\Step\Step;
use App\Services\Location\Geo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ApplicationService
{
    //use SectionPageChecker;

    private ?ApplicationData $applicationData = null;

    public function __construct(
        private readonly StoringApplication $storingApplication,
        private readonly ConfigNextStepBuilder $configNextStepBuilder,
        private readonly ApplicationRepository $applicationRepository,
    ) {
    }

    /**
     * @throws ApplicationNotUpdateableException
     * @throws \Exception
     */
    public function storeAndGetStep(ApplicationStoreDto $applicationStoreDto): Step
    {
        $geo = new Geo();
        $existingApplication = $this->storingApplication
            ->setMobile($applicationStoreDto->mobile)
            ->setSection($applicationStoreDto->sectionSlug)
            ->setPage($applicationStoreDto->pageSlug)
            ->setStep($applicationStoreDto->step)
            ->setRequesterType($applicationStoreDto->requesterType)
            ->setAgentId($applicationStoreDto->agentId)
            ->setGeo($geo->setLat($applicationStoreDto->lat)->setLng($applicationStoreDto->lon))
            ->store();

//        if ($existingApplication->status === Status::DRAFTED) {
//            return (new ApplicationNextStepBuilder($existingApplication))->getNextStep($applicationStoreDto->sectionSlug, $applicationStoreDto->pageSlug);
//        }

        $applicationData = $existingApplication->getApplicationData();
        $this->applicationData = $applicationData;

        if ($existingApplication->status === Status::RESUBMISSION_REQUESTED) {
            return $this->configNextStepBuilder->getNextResubmissionStep($applicationData, $existingApplication, $applicationStoreDto->sectionSlug, $applicationStoreDto->pageSlug);
        }

        return $this->calculateNextStep($applicationData, $applicationStoreDto->sectionSlug, $applicationStoreDto->pageSlug);
    }

    private function calculateNextStep(ApplicationData $applicationData, $sectionSlug, $pageSlug): Step
    {
        $nextStep = $this->configNextStepBuilder->getNextStep($applicationData, $sectionSlug, $pageSlug);
        $informationStep = null;
        if ($nextStep instanceof InformationStep) {
            $informationStep = $nextStep;
            $nextStep = $this->configNextStepBuilder->getNextStep($applicationData, $informationStep->sectionSlug, $informationStep->pageSlug);
        }
        $existingStep = $applicationData->getStep($nextStep->sectionSlug, $nextStep->pageSlug ?? null);
        if (! $existingStep) {
            return $informationStep ?? $nextStep;
        }

        return $this->calculateNextStep($applicationData, $existingStep->sectionSlug, $existingStep->pageSlug ?? null);
    }

    public function getApplicationData(): ?ApplicationData
    {
        return $this->applicationData;
    }

    /**
     * @throws \Exception
     */
    public function getInitialStep(): Step
    {
        return $this->configNextStepBuilder->getInitialStep();
    }

    /**
     * @throws InvalidSectionDataException
     */
    public function getStepFromApplicationData(ApplicationData $applicationData, string $sectionSlug, ?string $pageSlug = null): Step
    {
        $section = $applicationData->getSection($sectionSlug);

        if ($section === null) {
            throw new InvalidSectionDataException('Your application data doesn\'t have this step');
        }

        if ($pageSlug) {
            return $section->getStep($pageSlug)
                ?? throw new InvalidSectionDataException('Your application data doesn\'t have this step');
        }

        return $section->step;
    }

    public function getAllApplicationsByID(int $id, $current_month = null, $current_year = null, $status = null, $limit = null): ?Collection
    {
        return $this->applicationRepository->getAllApplicationsByID($id, $current_month, $current_year, $status, $limit);
    }

    public function getAllApplications(?string $status, ?array $agent_list): ?Collection
    {
        return $this->applicationRepository->getAllApplications($status, $agent_list);
    }

    public function getApplicationByID(int $id): Application
    {
        return $this->applicationRepository->getApplicationByID($id);
    }

    public function getApplicationLogsByID(int $id): Collection
    {
        return $this->applicationRepository->getApplicationByIDWithLogs($id);
    }

    public function formatLogDataForAdmin(array $application_data): array
    {
        $app_data = [];
        foreach ($application_data as $key => $a_data) {
            $a_data['from'] = $this->getSectionDataForAdmin(json_decode($a_data['from'], true));
            $a_data['to'] = json_decode($a_data['to'], true);
            $a_data['created_at'] = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $a_data['created_at'])->addHours(6)->format('jS M Y, h:i A');
            $app_data[] = $a_data;
        }

        return $app_data;
    }

    public function getSectionDataForAdmin(array $from_data): array
    {
        if ($from_data['section_slug'] === 'nid') {
            $from_data = ApplicationDataNidSection::fromArray($from_data);
        } elseif (array_key_exists('pages', $from_data)) {
            $from_data = ApplicationDataFormSection::fromArray($from_data);
        } else {
            $from_data = ApplicationDataGenericSection::fromArray($from_data);
        }

        return $from_data->toArrayForAdmin();
    }

    public function getApplicationArray(int $id): array
    {
        $application = $this->getApplicationByID($id);
        $application_ = $application->toArray();
        $application_['application_data'] = $application->getApplicationData()->toArrayForAdmin();
        $application_['log_data'] = $this->formatLogDataForAdmin($this->getApplicationLogsByID($id)->toArray());

        return $application_;
    }

    private function needToCheckFirstSubmissionAt(Status $status, ?Carbon $firstSubmissionAt): bool
    {
        if (($status == Status::SUBMITTED || Status::RESUBMITTED) && $firstSubmissionAt == null) {
            return false;
        }

        return true;
    }

    public function calculateStatisticsData($applicationsByAgent, $current_month, $current_year): array
    {
        $application_count = 0;
        $correction_count = 0;
        $pending_count = 0;
        $rejected_count = 0;
        $drafted_count = 0;

        foreach ($applicationsByAgent as $application) {
            if (array_key_exists('first_submission_at', $application) && 
                $application['first_submission_at'] !== null && 
                $application['first_submission_at'] !== '' && 
                $this->needToCheckFirstSubmissionAt($application['status'], $application['first_submission_at'])
            ) {
                $date = Carbon::createFromFormat('F d, Y', $application['first_submission_at']);
                $current_month = $date->format('m');
                $current_year = $date->format('Y');
            } else {
                $date = Carbon::createFromFormat('F d, Y', $application['created_at']);
                $current_month = $date->format('m');
                $current_year = $date->format('Y');
            }

            if ($current_month && $current_year) {
                if ($application['status'] == Status::RESUBMISSION_REQUESTED->getBanglaStatus()) {
                    $correction_count++;
                } elseif ($application['status'] == Status::REJECTED->getBanglaStatus()) {
                    $rejected_count++;
                } elseif ($application['status'] == Status::SUBMITTED->getBanglaStatus()) {
                    $pending_count++;
                } elseif ($application['status'] == Status::DRAFTED->getBanglaStatus()) {
                    $drafted_count++;
                }
                $application_count++;
            }
        }

        return $this->getStatisticsData(
            current_month: $current_month,
            application_count: $application_count,
            correction_count: $correction_count,
            pending_count: $pending_count,
            rejected_count: $rejected_count,
            drafted_count: $drafted_count
        );
    }

    public function getStatisticsData($current_month, $application_count, $correction_count, $pending_count, $rejected_count, $drafted_count): array
    {
        return [
            'month' => MonthArray::MONTHS_OF_YEAR[$current_month - 1],
            'data' => [
                [
                    'status' => 'application',
                    'count' => $application_count,
                    'translation' => 'অ্যাপ্লিকেশান',
                ],
                [
                    'status' => 'resubmission_requested',
                    'count' => $correction_count,
                    'translation' => 'সংশোধন',
                ],
                [
                    'status' => 'submitted',
                    'count' => $pending_count,
                    'translation' => 'পেন্ডিং',
                ],
                [
                    'status' => 'rejected',
                    'count' => $rejected_count,
                    'translation' => 'রিজেক্ট',
                ],
                [
                    'status' => 'drafted',
                    'count' => $drafted_count,
                    'translation' => 'অসম্পূর্ণ',
                ],
            ],
        ];
    }

    public function getFilterBySearch(array $applications, string $search_query): array
    {
        $allAgentApplications = [];
        foreach ($applications as $application) {
            if (str_contains(strtolower($application['farmer_name']), $search_query) || str_contains($application['id'], $search_query)) {
                $allAgentApplications[] = $application;
            }
        }

        return $allAgentApplications;
    }
}
