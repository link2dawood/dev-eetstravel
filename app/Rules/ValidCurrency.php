<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidCurrency implements Rule
{
    private $allowedCurrencies = [
        'USD', 'EUR', 'GBP', 'JPY', 'AUD', 'CAD', 'CHF', 'CNY', 'SEK', 'NZD',
        'MXN', 'SGD', 'HKD', 'NOK', 'TRY', 'RUB', 'INR', 'BRL', 'ZAR', 'KRW',
        'PLN', 'CZK', 'HUF', 'ILS', 'CLP', 'PHP', 'AED', 'EGP', 'SAR', 'THB'
    ];

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
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!$value) {
            return false;
        }

        // Check if it's a valid 3-letter currency code
        if (!preg_match('/^[A-Z]{3}$/', $value)) {
            return false;
        }

        // Check if it's in our allowed currencies list
        return in_array(strtoupper($value), $this->allowedCurrencies);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a valid currency code (e.g., USD, EUR, GBP).';
    }
}
