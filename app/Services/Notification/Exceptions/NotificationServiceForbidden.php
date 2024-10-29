<?php

namespace App\Services\Notification\Exceptions;

use Exception;
use Throwable;

class NotificationServiceForbidden extends Exception
{
    public function __construct($message = "Notification Service Forbidden.", $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}