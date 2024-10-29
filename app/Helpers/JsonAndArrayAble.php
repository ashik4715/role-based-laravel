<?php

namespace App\Helpers;

use Illuminate\Contracts\Support\Arrayable;

abstract class JsonAndArrayAble implements Arrayable
{
    abstract public static function fromArray(array $array): static;

    public static function fromJson(string $string): static
    {
        return static::fromArray(json_decode($string, 1));
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
