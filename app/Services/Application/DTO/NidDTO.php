<?php

namespace App\Services\Application\DTO;

use App\Helpers\JsonAndArrayAble;
use Illuminate\Support\Facades\Storage;

class NidDTO extends JsonAndArrayAble
{
    public function __construct(
        public readonly string $frontImage,
        public readonly string $backImage
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'front_image' => $this->frontImage,
            'back_image' => $this->backImage,
        ];
    }

    public function toArrayForApi(): array
    {
        return [
            'front_image' => Storage::url($this->frontImage),
            'back_image' => Storage::url($this->backImage),
        ];
    }

    /**
     * @param  array<string, string>  $array
     */
    public static function fromArray(array $array): static
    {
        return new static(
            frontImage: $array['front_image'],
            backImage: $array['back_image'],
        );
    }

    public static function uploadNidImages(array $images): self
    {
        $frontImage = $images['front_image']->store('nid');
        $backImage = $images['back_image']->store('nid');

        return new static($frontImage, $backImage);
    }
}
