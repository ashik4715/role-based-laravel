<?php

namespace App\Services\Application\DTO;

use App\Helpers\JsonAndArrayAble;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SelfieImagesDTO extends JsonAndArrayAble
{
    public function __construct(public readonly string $userImage)
    {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'user_image' => $this->userImage,
        ];
    }

    public function toArrayForApi(): array
    {
        return [
            'user_image' => Storage::url($this->userImage),
        ];
    }

    /**
     * @param  array<string, string>  $array
     */
    public static function fromArray(array $array): static
    {
        return new static($array['user_image']);
    }

    /**
     * @param  array<string, UploadedFile>  $images
     */
    public static function uploadNidImages(array $images): self
    {
        $userImage = $images['user_image']->store('user_images');
        return new static($userImage);
    }

    public function getUserImage(): string
    {
        return Storage::url($this->userImage);
    }
}
