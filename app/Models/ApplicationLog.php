<?php

namespace App\Models;

use App\Services\Application\ApplicationData\ApplicationData;
use App\Services\Application\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationLog extends Model
{
    use HasFactory;

    protected $fillable = ['application_id', 'type', 'section_slug', 'from', 'to', 'status', 'created_by_id', 'user_type'];

    protected $casts = [
        'status' => Status::class,
    ];

    public function getPreviousApplicationData(): ApplicationData
    {
        return ApplicationData::fromJson($this->from);
    }

    public function getNewApplicationData(): ApplicationData
    {
        return ApplicationData::fromJson($this->to);
    }
}
