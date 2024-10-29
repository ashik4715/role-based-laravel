<?php

namespace App\Services\Notification\Exceptions;

use Exception;
use Throwable;

class NotificationServiceNotWorking extends Exception
{
    public function __construct($message = "Notification Service not working.", $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}