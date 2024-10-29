<?php

namespace App\Rules;

use App\Services\BangladeshiMobileValidator;
use Illuminate\Contracts\Validation\InvokableRule;

class BdMobileNumber implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        if (BangladeshiMobileValidator::isInValid($value)) {
            $fail('The :attribute must be Bangladeshi Mobile Number.');
        }
    }
}
