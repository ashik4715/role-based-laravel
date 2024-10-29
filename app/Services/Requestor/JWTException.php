<?php

namespace App\Services\Requestor;

use Exception;
use Throwable;

class JWTException extends Exception
{
    public function __construct(string $message = "Invalid Token", int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
