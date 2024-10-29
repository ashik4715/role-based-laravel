<?php

namespace App\Helpers;

use ReflectionClass;

trait ConstGetter
{
    public static function getWithKeys(): array
    {
        $class = new ReflectionClass(static::class);

        return $class->getConstants();
    }

    public static function getWithKeysFlipped(): array
    {
        return array_flip(static::getWithKeys());
    }

    public static function get(): array
    {
        return array_values(static::getWithKeys());
    }

    public static function getAllWithout(...$excludes): array
    {
        if (is_array($excludes[0])) {
            $excludes = $excludes[0];
        }

        return array_diff(static::get(), $excludes);
    }

    public static function getWithMadeKeys(): array
    {
        $result = [];
        foreach (static::get() as $item) {
            $result[$item] = normalizeStringCases($item);
        }

        return $result;
    }

    public static function isValid($value): bool
    {
        return in_array($value, static::get());
    }

    public static function isInvalid($value): bool
    {
        return ! static::isValid($value);
    }

    public static function implode($glue = ','): string
    {
        return implode($glue, static::get());
    }
}
