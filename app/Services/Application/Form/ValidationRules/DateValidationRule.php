<?php

namespace App\Services\Application\Form\ValidationRules;

use App\Services\Application\Form\ValidationRuleException;
use Carbon\Carbon;

class DateValidationRule extends Rule
{

    public function __construct(
        public Carbon|string|null $before = null,
        public Carbon|string|null $after = null,
        bool $isRequired = true,
        ?string $isRequiredMessage = 'তথ্য দেওয়া অত্যাবশ্যক'
    )
    {
        parent::__construct(isRequired: $isRequired, isRequiredMessage: $isRequiredMessage);
    }



    public function toArray(): array
    {
        $data = parent::toArray();

        if ($this->before != null) {
            $data['before'] = $this->before instanceof Carbon ? $this->before->toDateString() : $this->before;
        }
        if ($this->after != null) {
            $data['after'] = $this->after instanceof Carbon ? $this->after->toDateString() : $this->after;
        }

        return $data;
    }

    public static function fromArray(array $array): static
    {
        return new static(
            before: $array['before'] ?? null,
            after: $array['after'] ?? null,
            isRequired: $array['is_required']['value'] ?? true,
            isRequiredMessage: $array['is_required']['message'] ?? null
        );
    }

    /**
     * @param  Carbon  $data
     * @return void
     *
     * @throws ValidationRuleException
     */
    public function validate(Carbon $data): void
    {
        if ($this->before != null) {
            $this->checkBefore($data);
        }
        if ($this->after != null) {
            $this->checkAfter($data);
        }
    }

    /**
     * @param  string|Carbon  $before
     * @return $this
     */
    public function setBefore(string|Carbon $before): DateValidationRule
    {
        $this->before = $before;

        return $this;
    }

    /**
     * @param  string|Carbon  $after
     * @return $this
     */
    public function setAfter(string|Carbon $after): DateValidationRule
    {
        $this->after = $after;

        return $this;
    }

    /**
     * @param  Carbon  $data
     * @return void
     *
     * @throws ValidationRuleException
     */
    private function checkBefore(Carbon $data): void
    {
        $before = $this->before instanceof Carbon ? $this->before : Carbon::parse($this->before);
        if ($data->lte($before)) {
            return;
        }
        throw new ValidationRuleException('Date should be bigger than '.$before->toDateString());
    }

    /**
     * @param  Carbon  $data
     * @return void
     *
     * @throws ValidationRuleException
     */
    private function checkAfter(Carbon $data): void
    {
        $after = $this->after instanceof Carbon ? $this->after : Carbon::parse($this->after);
        if ($data->gte($after)) {
            return;
        }
        throw new ValidationRuleException('Date should be bigger than '.$after->toDateString());
    }
}
