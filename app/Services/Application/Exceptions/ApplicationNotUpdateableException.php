<?php

namespace App\Services\Application\Exceptions;

class ApplicationNotUpdateableException extends \Exception
{
    protected $message = 'Application can not be updated';

    public function render()
    {
        return response(['message' => $this->message], $this->getCode() !== 0 ? $this->getCode() : 400);
    }
}
