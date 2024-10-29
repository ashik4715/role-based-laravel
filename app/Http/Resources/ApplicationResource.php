<?php

namespace App\Http\Resources;

use App\Models\Application;
use App\Services\Application\MonthArray;
use App\Services\Application\Status;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use JsonSerializable;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $application_data = json_decode($this->application_data, 1);
        $selfie_image = $application_data['nid']['selfie_images']['value']['user_image'] ?? '';
        $nid_name_eng = $application_data['nid']['farmer_info']['values']['nid_name_english']['value'] ?? $this->{Application::MOBILE};

        return [
            'id' => $this->id,
            'agent_id' => $this->agent_id,
            'farmer_name' => $nid_name_eng,
            'mobile' => $this->{Application::MOBILE},
            'status' => $this->status->getBanglaStatus(),
            'current_version' => $this->current_version,
            'selfie_image' => Storage::url($selfie_image),
            'created_at' => $this->created_at->format("F d, Y"),
            'updated_at' => $this->updated_at->format("F d, Y"),
        ];
    }
}
