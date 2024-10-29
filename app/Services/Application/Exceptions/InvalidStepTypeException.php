<?php

namespace App\Services\Application\Exceptions;

class InvalidStepTypeException extends \Exception
{
    protected $message = 'Invalid step type';

    public function render()
    {
        return response(['message' => $this->message], $this->getCode() !== 0 ? $this->getCode() : 400);
    }
}
