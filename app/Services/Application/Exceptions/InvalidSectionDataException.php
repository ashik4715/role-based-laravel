<?php

namespace App\Services\Application\Exceptions;

use Exception;

class InvalidSectionDataException extends Exception
{
    protected $message = 'Invalid section data';

    public function render()
    {
        return response(['message' => $this->message], $this->getCode() !== 0 ? $this->getCode() : 400);
    }
}
