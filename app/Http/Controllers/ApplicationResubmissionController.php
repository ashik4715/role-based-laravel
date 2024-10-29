<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminFormResubmitRequest;
use App\Models\Application;
use App\Repositories\ApplicationLog\ApplicationLogRepositoryInterface;
use App\Services\Application\Exceptions\InvalidResubmissionRequestException;
use App\Services\Application\ResubmissionRequest\ResubmissionRequestProcessor;
use App\Services\Application\Status;
use App\Services\Notification\Exceptions\NotificationServiceForbidden;
use App\Services\Notification\Exceptions\NotificationServiceNotWorking;
use App\Services\Notification\Service as NotificationService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;

class ApplicationResubmissionController extends Controller
{
    /**
     * @param Application $application
     * @param AdminFormResubmitRequest $request
     * @param ResubmissionRequestProcessor $resubmissionRequest
     * @param NotificationService $notificationService
     * @return Response
     * @throws InvalidResubmissionRequestException
     * @throws NotificationServiceForbidden
     * @throws NotificationServiceNotWorking
     * @throws GuzzleException
     */
    public function resubmitRequest(Application $application, AdminFormResubmitRequest $request, ResubmissionRequestProcessor $resubmissionRequest, NotificationService $notificationService, ApplicationLogRepositoryInterface $applicationLogRepository): Response
    {
        if ($application->status == Status::RESUBMISSION_REQUESTED) return response(['message' => 'This application is already in the resubmission process.'], 400);

        $previousStatus = $application->status;
        $applicationData = $application->replicate()->getApplicationData();
        $sections = [];
        foreach ($request->items as $item) {
            $sections[$item['section_slug']] = $applicationData->getSection($item['section_slug'])->toJson();
        }

        $resubmissionRequest->requestForResubmission($application, $request->getResubmissionRequestItems());

        $applicationLogRepository->create([
            'application_id' => $application->id,
            'type' => 'status_change',
            'from' => json_encode([$previousStatus]),
            'to' => json_encode([$application->status]),
            'created_by_id' => $request->get('created_by_id'),
            'user_type' => 'zonal_manager'
        ]);

        if ($application->status == Status::RESUBMISSION_REQUESTED) {
            foreach ($sections as $slug => $item) {
                $newApplicationData = $application->getApplicationData();
                $newSection = $newApplicationData->getSection($slug)->toJson();
                $applicationLogRepository->create([
                    'application_id' => $application->id,
                    'type' => 'application_data',
                    'section_slug' => $slug,
                    'from' => $item,
                    'to' => $newSection,
                    'status' => Status::RESUBMISSION_REQUESTED,
                    'created_by_id' => $request->get('created_by_id'),
                    'user_type' => 'zonal_manager'
                ]);
            }
        }
        $notificationService->statusUpdateNotification($application->agent_id, $application);


        return response(['message' => 'Resubmission request was successful'], 200);
    }
}
