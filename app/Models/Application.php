<?php

namespace App\Models;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Status $status
 * @property int $current_version
 * @property string $application_data
 */
class Application extends Model
{
    use HasFactory;

    protected $fillable = ['column_1', 'application_data', 'status', 'agent_id', 'note', 'address'];

    protected $casts = [
        'status' => Status::class,
    ];

    const MOBILE = 'column_1';

    public function getApplicationData(): ApplicationData
    {
        return ApplicationData::fromJson($this->application_data);
    }

    public function isSubmitted(): bool
    {
        return $this->status === Status::SUBMITTED;
    }

    public function isResubmissionRequested(): bool
    {
        return $this->status === Status::RESUBMISSION_REQUESTED;
    }

    public function isResubmitted(): bool
    {
        return $this->status === Status::RESUBMITTED;
    }

    public function isInitiated(): bool
    {
        return $this->status === Status::INITIATED;
    }

    public function isDrafted(): bool
    {
        return $this->status === Status::DRAFTED;
    }

    public function isCurrentStatusEligibleForResubmissionRequest(): bool
    {
        return $this->isSubmitted() || $this->isResubmissionRequested() || $this->isResubmitted();
    }

    public function getCurrentVersion(): int
    {
        return $this->current_version;
    }

    public function isUpdateable()
    {
        return $this->isResubmissionRequested() || $this->isDrafted() || $this->isInitiated();
    }

    public function getFormattedCreatedAt($format = 'Y-m-d H:i:s')
    {
        return $this->created_at->format($format);
    }

    public function getFormattedUpdatedAt($format = 'Y-m-d H:i:s')
    {
        return $this->updated_at->format($format);
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function ($w) use ($searchTerm) {
            $w
                ->orWhere(self::MOBILE, 'LIKE', "%$searchTerm%")
                ->orWhere('column_2', 'LIKE', "%$searchTerm%");
        });
    }

    public function getAddress()
    {
        return $this->address != null ? json_decode($this->address) : null;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }
}
