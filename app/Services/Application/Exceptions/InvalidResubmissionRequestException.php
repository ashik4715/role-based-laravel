<?php

namespace App\Services\Application\Exceptions;

use Exception;

class InvalidResubmissionRequestException extends Exception
{
    protected $message = 'Invalid resubmission request';

    public function render()
    {
        return response(['message' => $this->message], $this->getCode() !== 0 ? $this->getCode() : 400);
    }
}
