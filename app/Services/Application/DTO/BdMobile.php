<?php

namespace App\Services\Application\DTO;

use App\Services\BangladeshiMobileValidator;

class BdMobile
{
    private readonly string $data;

    /**
     * @param  string  $data
     *
     * @throws \Exception
     */
    public function __construct(string $data)
    {
        $this->data = BangladeshiMobileValidator::validate($data);
    }

    /**
     * @return string
     */
    public function getFullNumber(): string
    {
        return $this->data;
    }

    public function getNumberWithoutPrefix(): string
    {
        return str_starts_with($this->data, '+88') ? substr($this->data, 3) : $this->data;
    }
}
