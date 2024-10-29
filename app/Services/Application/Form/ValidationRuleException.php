<?php

namespace App\Services\Application\Form;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ValidationRuleException extends \Exception
{
    public function __construct($message = 'Data Not Validated', $code = Response::HTTP_BAD_REQUEST, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return response(['message' => $this->message], $this->getCode() !== 0 ? $this->getCode() : 400);
    }
}
