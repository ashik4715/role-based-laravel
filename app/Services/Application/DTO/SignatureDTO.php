<?php

namespace App\Services\Application\DTO;

use App\Helpers\JsonAndArrayAble;

class SignatureDTO extends JsonAndArrayAble
{
    /**
     * @param  string  $image
     */
    public function __construct(
        public readonly string $image,
    ) {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'image' => $this->image,
        ];
    }

    public static function fromArray(array $array): static
    {
        return new static(
            image: $array['image'],
        );
    }

    public static function uploadSignature(array $array): self
    {
        $signature = $array['image']->store('signatures');

        return new static($signature);
    }
}
