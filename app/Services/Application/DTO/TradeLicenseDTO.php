<?php

namespace App\Services\Application\DTO;

use App\Helpers\JsonAndArrayAble;

class TradeLicenseDTO extends JsonAndArrayAble
{
    /**
     * @param  string  $tradeLicenseImage
     */
    public function __construct(
        public readonly string $tradeLicenseImage,
    ) {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'trade_license_image' => $this->tradeLicenseImage,
        ];
    }

    public static function fromArray(array $array): static
    {
        return new static(
            tradeLicenseImage: $array['trade_license_image'],
        );
    }

    public static function uploadTradeLicense(array $array): self
    {
        $tradeLicense = $array['trade_license_image']->store('trade_licenses');

        return new static($tradeLicense);
    }
}
