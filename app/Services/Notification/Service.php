<?php

namespace App\Services\Notification;

use App\Models\Application;
use App\Services\Application\Status;
use App\Services\Notification\Exceptions\NotificationServiceForbidden;
use App\Services\Notification\Exceptions\NotificationServiceNotWorking;
use GuzzleHttp\Exception\GuzzleException;

class Service
{
    /** @var Client $client */
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     * @throws NotificationServiceNotWorking
     * @throws GuzzleException|NotificationServiceForbidden
     */
    public function getAllNotifications(): array
    {
        $uri = "notifications";
        return $this->client->get($uri);
    }

    /**
     * @param $notification_id
     * @return array
     * @throws GuzzleException
     * @throws NotificationServiceNotWorking|NotificationServiceForbidden
     */
    public function readNotificationAsMarked($notification_id): array
    {
        $uri = "notifications/$notification_id/read";
        return $this->client->put($uri, []);
    }

    /**
     * @throws GuzzleException
     * @throws NotificationServiceForbidden
     * @throws NotificationServiceNotWorking
     */
    public function statusUpdateNotification($agent_id, Application $application): array
    {
        $uri = "notifications";
        $status = $application->status;
        $title = "";
        $description = "";
        $img_url = null;
        if ($status == Status::REJECTED) {
            $title = "নতুন নোটিফিকেশন";
            $description = "আপনার অ্যাপ্লিকেশন (ID#" . $application->id . ") বাতিল করা হয়েছে!";
            $img_url = "https://wegro-agent-onboarding.s3.ap-southeast-1.amazonaws.com/notification-icons/rejected.png";
        }
        if ($status == Status::APPROVED) {
            $title = "নতুন নোটিফিকেশন";
            $description = "আপনার অ্যাপ্লিকেশন (ID#" . $application->id . ") গ্রহণ করা হয়েছে!";
            $img_url = "https://wegro-agent-onboarding.s3.ap-southeast-1.amazonaws.com/notification-icons/approved.png";
        }
        if ($status == Status::RESUBMISSION_REQUESTED) {
            $title = "নতুন নোটিফিকেশন";
            $description = "আপনার অ্যাপ্লিকেশন (ID#" . $application->id . ") পুনঃ সম্পাদনার জন্য পাঠানো হয়েছে !";
            $img_url = "https://wegro-agent-onboarding.s3.ap-southeast-1.amazonaws.com/notification-icons/resubmission.png";
        }
        $data = [
            'title' => $title,
            'description' => $description,
            'channels' => ['inApp', 'push'],
            'payload' => json_encode([
                'mobile' => $application->column_1,
                'status' => $status->getReadableStatus()
            ]),
            'user_identifier' => $agent_id,
            'user_type' => 'agent',
            'created_by' => 'Zonal Manager',
            'img_url' => $img_url
        ];

        return $this->client->isCommunicateViaXApiKey(true)->post($uri, $data);
    }
}
