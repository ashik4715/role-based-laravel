<?php

namespace App\Helpers;

use Exception;
use ReflectionEnum;

trait ReverseEnum
{
    public static function tryFrom($caseName): ?self
    {
        $rc = new ReflectionEnum(self::class);

        return $rc->hasCase($caseName) ? $rc->getConstant($caseName) : null;
    }

    /**
     * @throws Exception
     */
    public static function from($caseName): self
    {
        return self::tryFrom($caseName) ?? throw new Exception('Enum '.$caseName.' not found in '.self::class);
    }
}
