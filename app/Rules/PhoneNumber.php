<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneNumber implements Rule
{
    public function passes($attribute, $value)
    {
        // Updated regular expression to accept +9665******** and 05******** formats
        $pattern = '/^((\+9665\d{8})|(05\d{8}))$/';
        return preg_match($pattern, $value);
    }

    public function message()
    {
        // Arabic translation of the validation message
        $phone_number = '+966566193395 or 0566193395';

        return __(':attribute must be a valid phone number. Example: ') . $phone_number;
    }
}
