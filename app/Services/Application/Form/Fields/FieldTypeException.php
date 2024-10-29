<?php

namespace App\Services\Application\Form\Fields;

use App\Services\GenericExceptionRenderer;

class FieldTypeException extends \Exception
{
    use GenericExceptionRenderer;
    protected $message = 'Invalid field type';
}
