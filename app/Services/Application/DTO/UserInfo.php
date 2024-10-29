<?php

namespace App\Services\Application\DTO;

use Illuminate\Contracts\Support\Arrayable;

class UserInfo implements Arrayable
{
    public function __construct(public readonly string $name, public readonly BdMobile $mobile, public readonly string $image)
    {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'mobile' => $this->mobile->getFullNumber(),
            'image' => $this->image
        ];
    }
}
