<?php

namespace App\Services;

trait GenericExceptionRenderer
{
    public function render()
    {
        return response(['message' => $this->message], $this->getCode() !== 0 ? $this->getCode() : 400);
    }
}
