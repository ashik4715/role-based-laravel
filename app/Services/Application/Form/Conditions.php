<?php

namespace App\Services\Application\Form;

use App\Helpers\JsonAndArrayAble;

class Conditions extends JsonAndArrayAble
{
    /**
     * @param Condition[] $conditions
     */
    public function __construct(public readonly array $conditions)
    {
    }

    public function toArray()
    {
        // TODO: Implement toArray() method.
    }

    public static function fromArray(array $array): static
    {
        // TODO: Implement fromArray() method.
    }
}
