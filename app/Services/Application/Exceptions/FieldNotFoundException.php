<?php

namespace App\Services\Application\Exceptions;

class FieldNotFoundException extends \Exception
{
    protected $message = 'Field not found';

    public function render()
    {
        return response(['message' => $this->message], $this->getCode() !== 0 ? $this->getCode() : 400);
    }
}
