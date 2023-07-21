<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Slug implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->hasUnderscores($value)) {
            $fail('validation.no_underscores')->translate();
        }

        if ($this->startsWithDashes($value)) {
            $fail('validation.no_starting_dashes')->translate();
        }

        if ($this->endsWithDashes($value)) {
            $fail('validation.no_ending_dashes')->translate();
        }
    }

    /**
     * @return false|int
     */
    protected function hasUnderscores($value)
    {
        return preg_match('/_/', $value);
    }

    /**
     * @return false|int
     */
    protected function startsWithDashes($value)
    {
        return preg_match('/^-/', $value);
    }

    /**
     * @return false|int
     */
    protected function endsWithDashes($value)
    {
        return preg_match('/-$/', $value);
    }
}
