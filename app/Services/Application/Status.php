<?php

namespace App\Services\Application;

use App\Helpers\ReverseEnum;

enum Status: string
{
    use ReverseEnum, EnumsToArray;

    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case DRAFTED = 'drafted';
    case INITIATED = 'initiated';
    case SUBMITTED = 'submitted';
    case RESUBMISSION_REQUESTED = 'resubmission_requested';
    case RESUBMITTED = 'resubmitted';

    /**
     * @return string
     */
    public function getReadableStatus(): string
    {
        return match ($this) {
            Status::SUBMITTED, Status::RESUBMITTED => 'Pending',
            Status::REJECTED => 'Rejected',
            Status::APPROVED => 'Approved',
            Status::INITIATED => 'Initiated',
            Status::DRAFTED => 'Drafted',
            Status::RESUBMISSION_REQUESTED => 'Correction'
        };
    }

    public function getBanglaStatus(): string
    {
        return match ($this) {
            Status::SUBMITTED, Status::RESUBMITTED => 'পেন্ডিং',
            Status::REJECTED => 'রিজেক্ট',
            Status::APPROVED => 'অনুমোদিত',
            Status::INITIATED => 'প্রক্রিয়াধীন',
            Status::DRAFTED => 'অসম্পূর্ণ',
            Status::RESUBMISSION_REQUESTED => 'সংশোধন'
        };
    }

    /**
     * @return string[]
     */
    public static function getAvailableStatusToFilter(): array
    {
        return [
            Status::RESUBMISSION_REQUESTED->value => Status::RESUBMISSION_REQUESTED->getBanglaStatus(),
            Status::DRAFTED->value => Status::DRAFTED->getBanglaStatus(),
            Status::APPROVED->value => Status::APPROVED->getBanglaStatus(),
            Status::REJECTED->value => Status::REJECTED->getBanglaStatus(),
            Status::SUBMITTED->value => Status::SUBMITTED->getBanglaStatus()
        ];
    }
}

