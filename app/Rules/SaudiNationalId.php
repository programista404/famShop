<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SaudiNationalId implements Rule
{
    public function passes($attribute, $value)
    {
        // Ensure the National ID has the correct length
        if (strlen($value) != 10) {
            return false;
        }

        // Extract the region or city code
        $regionCode = substr($value, 0, 1);

        // Validate the region or city code (customize as needed)
        // For example, you can create an array of valid codes
        $validRegionCodes = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];
        if (!in_array($regionCode, $validRegionCodes)) {
            return false;
        }

        // Other validation logic as needed...

        // If all checks pass, consider the National ID valid
        return true;
    }

    public function message()
    {
        return 'The :attribute is not a valid  National ID.';
    }
}
