<?php

namespace App\Http\Controllers;

use App\Services\Notification\Exceptions\NotificationServiceForbidden;
use App\Services\Notification\Exceptions\NotificationServiceNotWorking;
use App\Services\Notification\Service;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    private Service $notificationService;

    public function __construct(Service $service)
    {
        $this->notificationService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return Response
     * @throws GuzzleException
     * @throws NotificationServiceNotWorking|NotificationServiceForbidden
     */
    public function index(Request $request): Response
    {
        $notifications = $this->notificationService->getAllNotifications();
        return response($notifications, 200);
    }

    /**
     * @param $notification_id
     * @param  Request  $request
     * @return Response
     * @throws GuzzleException
     */
    public function update($notification_id, Request $request): Response
    {
        try {
            $notifications = $this->notificationService->readNotificationAsMarked($notification_id);
            return response($notifications, 200);
        } catch (NotificationServiceForbidden $e) {
            return response(['message' => "Already read notification can't be read again"], 403);
        } catch (NotificationServiceNotWorking $e) {
            return response(['message' => "Notification service not working"], 503);
        }
    }
}
