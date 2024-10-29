<?php

namespace App\Repositories\Application;

use App\Models\Application;
use App\Models\ApplicationLog;
use App\Repositories\BaseRepository;
use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\DTO\BdMobile;
use App\Services\Application\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ApplicationRepository extends BaseRepository implements ApplicationRepositoryInterface
{
    protected $model;

    public function __construct(Application $model)
    {
        $this->model = $model;
        parent::__construct($model);
    }

    public function getLatestApplicationByMobile(BdMobile $mobile): ?Application
    {
        return $this->model->where(Application::MOBILE, $mobile->getFullNumber())->latest()->first();
    }

    public function storeResubmissionRequest(Application $application, ApplicationData $applicationData): void
    {
        $application->application_data = $applicationData->toJson();
        $application->status = Status::RESUBMISSION_REQUESTED;
        $application->save();
    }

    public function getAllApplicationsByID(int $id, $current_month = null, $current_year = null, $status = null, $limit = null): ?Collection
    {
        $query = $this->model->where('agent_id', $id);

        if ($current_month && $current_year) $query->whereMonth('created_at', $current_month)->whereYear('created_at', $current_year);

        if ($status) if ($status == Status::SUBMITTED->value) {
            $query->whereIn('status', [Status::RESUBMITTED, Status::SUBMITTED]);
        } else {
            $query->where('status', '=', $status);
        } else $query->where('status', '<>', Status::INITIATED);
        $query->orderByRaw("FIELD(status , 'resubmission_requested') DESC")->orderBy('id', 'DESC');
        if ($limit) $query->limit($limit);
        return $query->get();
    }

    public function getAllApplications(?string $status, ?array $agent_list): ?Collection
    {
        $applicationsQuery = $this->model::whereNotIn('status', [Status::INITIATED, Status::DRAFTED]);

        if ($status) if ($status == Status::SUBMITTED->value) {
            $applicationsQuery->whereIn('status', [Status::RESUBMITTED, Status::SUBMITTED]);
        } else $applicationsQuery->where('status', '=', $status);

        if(isset($agent_list)){
            return  $applicationsQuery->whereIn('agent_id', $agent_list)->get();
        }
        return $applicationsQuery->get();
    }

    public function getApplicationByID(int $id): Application
    {
        return $this->model->findOrFail($id);
    }

    public function getApplicationByIDWithLogs(int $id): Collection
    {
        return ApplicationLog::where('application_id', $id)
            ->where('type', 'application_data')
            ->orderBy('id', 'desc')
            ->get();
    }
}
