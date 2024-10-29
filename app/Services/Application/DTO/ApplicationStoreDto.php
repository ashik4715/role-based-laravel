<?php

namespace App\Services\Application\DTO;

use App\Helpers\JsonAndArrayAble;
use App\Services\Application\RequesterType;
use App\Services\Application\Step\Step;

class ApplicationStoreDto extends JsonAndArrayAble
{
    public function __construct(
        public readonly BdMobile $mobile,
        public readonly RequesterType $requesterType,
        public readonly string $sectionSlug,
        public readonly ?string $pageSlug,
        public readonly Step $step,
        public readonly int $agentId,
        public readonly ?float $lat,
        public readonly ?float $lon
    ) {
    }

    public function toArray(): array
    {
        return [
            'mobile' => $this->mobile,
            'requester_type' => $this->requesterType,
            'section_slug' => $this->sectionSlug,
            'page_slug' => $this->pageSlug,
            'step' => $this->step,
            'agent_id' => $this->agentId,
        ];
    }

    public static function fromArray(array $array): static
    {
        return new static(
            mobile: $array['mobile'],
            requesterType: $array['requester_type'],
            sectionSlug: $array['section_slug'],
            pageSlug: $array['page_slug'],
            step: $array['step'],
            agentId: $array['agent_id'],
            lat: $array['lat'],
            lon: $array['lon']
        );
    }
}
