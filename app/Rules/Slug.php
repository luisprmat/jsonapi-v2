<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Slug implements Rule
{
    private string $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if ($this->hasUnderscores($value)) {
            $this->message = __('validation.no_underscores');
            return false;
        }

        if ($this->startsWithDashes($value)) {
            $this->message = __('validation.no_starting_dashes');
            return false;
        }

        if ($this->endsWithDashes($value)) {
            $this->message = __('validation.no_ending_dashes');
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * @param $value
     * @return false|int
     */
    protected function hasUnderscores($value)
    {
        return preg_match('/_/', $value);
    }

    /**
     * @param $value
     * @return false|int
     */
    protected function startsWithDashes($value)
    {
        return preg_match('/^-/', $value);
    }

    /**
     * @param $value
     * @return false|int
     */
    protected function endsWithDashes($value)
    {
        return preg_match('/-$/', $value);
    }
}
