<?php

namespace App\Services;

use Exception;
use Throwable;

class InvalidMobileNumberException extends Exception
{
    use GenericExceptionRenderer;
    public function __construct(string $message = "Invalid Mobile Number", int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
