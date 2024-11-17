<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationSubmitRequest;
use App\Http\Requests\GetStepRequest;
use App\Http\Requests\ReviewStepGetRequest;
use App\Http\Resources\ApplicationResource;
use App\Http\Resources\ReviewStepResource;
use App\Http\Resources\StepResource;
use App\Models\Application;
use App\Repositories\ApplicationLog\ApplicationLogRepositoryInterface;
use App\Services\Application\ApplicationService;
use App\Services\Application\Form\PreloadedData\PreloadedData;
use App\Services\Application\SectionService;
use App\Services\Application\Status;
use App\Services\Notification\Exceptions\NotificationServiceForbidden;
use App\Services\Notification\Exceptions\NotificationServiceNotWorking;
use App\Services\Notification\Service as NotificationService;
use App\Services\Requestor\JWTRequest;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Http;

class ApplicationController extends Controller
{
    public function viewJson($id)
    {
        try {
            $response = Http::withHeaders([
                'api-key' => 'wegro_control_service',
            ])->get('http://192.168.0.167:7002/api/wcp-applications');

            if ($response->successful()) {
                $applications = $response->json();
                
                // Initialize variables
                $data = null;
                $single_application = null;
                
                // Validate response structure
                if (is_array($applications) && !empty($applications)) {
                    $application = $applications[0];
                    
                    // Check if application_data exists and is valid JSON
                    if (isset($application['application_data'])) {
                        // Decode JSON string to array
                        $data = is_string($application['application_data']) 
                            ? json_decode($application['application_data'], true) 
                            : $application['application_data'];
                            
                        $single_application = $application;                        
                    }
                }
                // dd( $data);

                return view('backend.pages.dashboard.view-json', [
                    'data' => $data,
                    'single_application' => $single_application
                ]);
            }
            
            return view('backend.pages.dashboard.view-json', [
                'data' => null,
                'single_application' => null,
                'error' => 'API request failed'
            ]);
            
        } catch (\Exception $e) {
            return view('backend.pages.dashboard.view-json', [
                'data' => null,
                'single_application' => null,
                'error' => 'Error processing data: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $application = Application::findOrFail($id);
            $application->delete();

            return redirect()->back()->with('success', 'Application deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete application: '.$e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function next(ApplicationSubmitRequest $request, ApplicationService $applicationService): StepResource
    {
        if ($request->mobile) {
            $step = $applicationService->storeAndGetStep($request->getData());
        } else {
            $step = $applicationService->getInitialStep();
        }

        return new StepResource($step, $applicationService->getApplicationData());
    }

    public function getReviewStep(ReviewStepGetRequest $request, SectionService $sectionService): ReviewStepResource
    {
        $application = $request->getApplication();
        $applicationData = $application->getApplicationData();

        return new ReviewStepResource($sectionService->getReviewStep($applicationData), $application);
    }

    /**
     * @throws Exception
     */
    public function getStepFromApplicationData(GetStepRequest $request, ApplicationService $applicationService): StepResource
    {
        $step = $applicationService->getStepFromApplicationData($request->getApplication()->getApplicationData(), $request->section_slug, $request->page_slug);

        return new StepResource($step);
    }

    /**
     * @throws Exception
     */
    public function getAllApplicationsByAgent(Request $request, ApplicationService $applicationService): JsonResponse
    {
        $agent_id = JWTRequest::getAgentId();

        $current_month = $request->month ? $request->month : Carbon::now()->month;
        $current_year = $request->year ? $request->year : Carbon::now()->year;
        $status = $request->status ?? null;
        $search_query = $request->q ?? null;
        [$offset, $limit] = calculatePagination($request);
        $allAgentApplications = ApplicationResource::collection(
            $applicationService->getAllApplicationsByID(id: $agent_id, current_month: $current_month, current_year: $current_year, status: $status, limit: $limit))
            ->toArray($request);

        if ($search_query) {
            $allAgentApplications = $applicationService->getFilterBySearch($allAgentApplications, strtolower($search_query));
        }

        return response()->json([
            'available_status' => Status::getAvailableStatusToFilter(),
            'applications' => $allAgentApplications,
        ]);
    }

    public function getAllApplications(Request $request, ApplicationService $applicationService): array
    {
        $status = $request->status ?? null;

        return $applicationService->getAllApplications($status, null)->toArray();
    }

    public function show($id, Request $request, ApplicationService $applicationService): array
    {
        return $applicationService->getApplicationArray($id);
    }

    /**
     * @param $id
     * @param  Request  $request
     * @param  ApplicationService  $applicationService
     * @param  NotificationService  $notificationService
     * @param  ApplicationLogRepositoryInterface  $applicationLogRepository
     * @return JsonResponse
     *
     * @throws GuzzleException
     * @throws NotificationServiceForbidden
     * @throws NotificationServiceNotWorking
     */
    public function updateStatus($id, Request $request, ApplicationService $applicationService, NotificationService $notificationService, ApplicationLogRepositoryInterface $applicationLogRepository): JsonResponse
    {
        $application = $applicationService->getApplicationByID($id);
        $agent_id = $application->agent_id;
        $previousStatus = $application->status;
        if ($request->status == Status::REJECTED->value) {
            $request->validate([
                'status' => ['required', Rule::in(Status::REJECTED->value)],
                'rejection_note' => ['required', 'string'],
            ]);
            $application->update(['status' => $request->status, 'note' => $request->rejection_note]);
        } else {
            $request->validate(['status' => ['required', Rule::in(Status::APPROVED->value)]]);
            $application->update(['status' => $request->status]);
        }

        $applicationLogRepository->create([
            'application_id' => $application->id,
            'type' => 'status_change',
            'from' => json_encode([$previousStatus]),
            'to' => json_encode([$application->status]),
            'created_by_id' => $request->get('created_by_id'),
            'user_type' => 'zonal_manager',
        ]);

        $notificationService->statusUpdateNotification($agent_id, $application);

        return response()->json(['message' => 'Status Updated'], 200);
    }

    /**
     * @throws Exception
     */
    public function dataTable(Request $request, ApplicationService $applicationService): JsonResponse
    {
        $searchTerm = $request->search;

        if ($request->has('agent_list')) {
            $agent_list = $request->input('agent_list');
            $agent_list = is_null($agent_list) ? [] : explode(',', $request->input('agent_list'));
        } else {
            $agent_list = null;
        }

        return Datatables::of($applicationService->getAllApplications($request->input('status'), $agent_list))
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                if ($row->status === Status::APPROVED) {
                    return '<span class="badge badge-success">'.$row->status->getReadableStatus().'</span>';
                } elseif ($row->status === Status::REJECTED) {
                    return '<span class="badge badge-danger">'.$row->status->getReadableStatus().'</span>';
                } elseif ($row->status === Status::RESUBMITTED) {
                    return '<span class="badge badge-info">'.$row->status->getReadableStatus().'</span>';
                } elseif ($row->status === Status::RESUBMISSION_REQUESTED) {
                    return '<span class="badge badge-warning">'.$row->status->getReadableStatus().'</span>';
                }

                return '<span class="badge badge-info">'.$row->status->getReadableStatus().'</span>';
            })
            ->addColumn('created_at', function (Application $row) {
                return $row->getFormattedCreatedAt('jS M Y, h:i A');
            })
            ->addColumn('updated_at', function (Application $row) {
                return $row->getFormattedUpdatedAt('jS M Y, h:i A');
            })
            ->filter(function ($instance) use ($searchTerm) {
                if (empty($searchTerm)) {
                    return;
                }

                $instance->search($searchTerm);
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    public function getStatistics(Request $request, ApplicationService $applicationService): JsonResponse
    {
        $agent_id = JWTRequest::getAgentId();
        $current_month = $request->month ?? Carbon::now()->month;
        $current_year = $request->year ?? Carbon::now()->year;

        $applicationsByAgent = ApplicationResource::collection($applicationService->getAllApplicationsByID($agent_id, $current_month, $current_year))->toArray($request);

        return response()->json($applicationService->calculateStatisticsData(
            applicationsByAgent: $applicationsByAgent,
            current_month: $current_month,
            current_year: $current_year), status: 200);
    }

    public function preloadData(Request $request)
    {
        $preloadedData = PreloadedData::getData();

        $preloadedData = $this->removeNullValues($preloadedData);

        return response()->json($preloadedData);
    }

    private function removeNullValues($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->removeNullValues($value);
            }

            $data = array_filter($data, function ($value) {
                return ! is_null($value);
            });
        }

        return $data;
    }
}
