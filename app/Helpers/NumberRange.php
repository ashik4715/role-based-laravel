<?php

namespace App\Helpers;

class NumberRange extends JsonAndArrayAble
{
    public function __construct(public readonly int|float|null $min = null, public readonly int|float|null $max = null, public readonly array $lengths = [])
    {
        if (count($this->lengths) > 0 && ($max !== null || $min !== null)) {
            throw new \InvalidArgumentException('If the equals parameter is given, min and max have to be empty');
        }
        if ($max == null && $min == null && count($lengths) === 0) {
            throw new \InvalidArgumentException('All the parameters cannot be empty for number range');
        }
        if ($min != null && $max != null && $max < $min) {
            throw new \InvalidArgumentException('Max can not be smaller than Min');
        }
    }

    public function toArray(): array
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
            'lengths' => $this->lengths,
        ];
    }

    public static function fromArray(array $array): static
    {
        return new static($array['min'], $array['max'], $array['lengths']);
    }

    public function isInRange(int|float $value): bool
    {
        if (count($this->lengths) > 0) {
            return in_array($value, $this->lengths, true);
        }

        if ($this->min != null && $value < $this->min) {
            return false;
        }
        if ($this->max != null && $value > $this->max) {
            return false;
        }

        return true;
    }

    public function isNotInRange(int|float $value): bool
    {
        return ! $this->isInRange($value);
    }

    public function toString(string $glue = '&'): string
    {
        if (count($this->lengths) > 0) {
            return (count($this->lengths) > 1 ? 'one of ' : '').implode(',', $this->lengths);
        }

        if ($this->min != null && $this->max == null) {
            return 'minimum '.$this->min;
        }

        if ($this->min == null && $this->max != null) {
            return 'maximum '.$this->max;
        }

        return 'between '.$this->min." $glue ".$this->max;
    }
}
