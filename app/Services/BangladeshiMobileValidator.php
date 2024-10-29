<?php

namespace App\Services;

class BangladeshiMobileValidator
{
    public static function isValid($number): bool
    {
        if (! $number) {
            return false;
        }

        return self::isBangladeshiNumberFormat(self::format($number));
    }

    public static function isInValid($number): bool
    {
        return ! self::isValid($number);
    }

    /**
     * @throws \Exception
     */
    public static function validate($number): string
    {
        if (self::isInValid($number)) {
            throw new InvalidMobileNumberException('Not a valid Bangladeshi number');
        }

        return self::format($number);
    }

    public static function format($number): string
    {
        $number = str_replace(' ', '', $number);

        if (preg_match("/^(\+88)/", $number)) {
            return $number;
        }
        if (preg_match('/^(88)/', $number)) {
            return preg_replace('/^88/', '+88', $number);
        }
        if (preg_match('/^([1-9])/', $number)) {
            return '+880'.$number;
        }

        return '+88'.$number;
    }

    private static function isBangladeshiNumberFormat($number): bool
    {
        return self::contains88($number) && strlen($number) == 14 && $number[4] == '1' && self::inBdNumberDomain($number);
    }

    private static function contains88($number): bool
    {
        return str_starts_with($number, '+880');
    }

    private static function inBdNumberDomain($number): bool
    {
        return in_array($number[5], [3, 4, 5, 6, 7, 8, 9]);
    }
}
