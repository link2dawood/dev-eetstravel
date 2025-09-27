<?php

namespace App\Services;

use App\Rules\ValidCurrency;
use App\Rules\ValidPhoneNumber;
use App\Rules\ValidBusinessEmail;
use App\Rules\ValidTourDateRange;

/**
 * Centralized Validation Service
 * Provides common validation rules and patterns for the entire application
 */
class ValidationService
{
    /**
     * Common validation rules for names and titles
     */
    public static function nameRules($required = true, $maxLength = 191, $minLength = 2)
    {
        $rules = [
            'string',
            'max:' . $maxLength,
            'min:' . $minLength,
            'regex:/^[a-zA-Z\s\-\.\']+$/' // Letters, spaces, hyphens, dots, apostrophes
        ];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Common validation rules for business names and companies
     */
    public static function businessNameRules($required = true, $maxLength = 191, $minLength = 2)
    {
        $rules = [
            'string',
            'max:' . $maxLength,
            'min:' . $minLength,
            'regex:/^[a-zA-Z0-9\s\-\.\&\(\)]+$/' // Letters, numbers, spaces, common business symbols
        ];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Common validation rules for phone numbers
     */
    public static function phoneRules($required = true)
    {
        $rules = [
            'string',
            'max:20',
            'regex:/^[\+]?[0-9\s\-\(\)]+$/' // International phone format
        ];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Common validation rules for email addresses
     */
    public static function emailRules($required = true, $unique = null)
    {
        $rules = [
            'email',
            'max:191'
        ];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        if ($unique) {
            $rules[] = 'unique:' . $unique;
        }

        return $rules;
    }

    /**
     * Common validation rules for addresses
     */
    public static function addressRules($required = true, $maxLength = 500, $minLength = 5)
    {
        $rules = [
            'string',
            'max:' . $maxLength,
            'min:' . $minLength
        ];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Common validation rules for monetary amounts
     */
    public static function moneyRules($required = true, $min = 0, $max = 999999.99)
    {
        $rules = [
            'numeric',
            'min:' . $min,
            'max:' . $max
        ];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Common validation rules for passenger counts
     */
    public static function passengerRules($required = true, $min = 1, $max = 500)
    {
        $rules = [
            'integer',
            'min:' . $min,
            'max:' . $max
        ];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Common validation rules for dates
     */
    public static function dateRules($required = true, $after = 'today', $before = null)
    {
        $rules = ['date'];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        if ($after) {
            $rules[] = 'after_or_equal:' . $after;
        }

        if ($before) {
            $rules[] = 'before_or_equal:' . $before;
        }

        return $rules;
    }

    /**
     * Common validation rules for file uploads
     */
    public static function fileRules($required = false, $maxSize = 10240, $allowedTypes = null)
    {
        $defaultTypes = 'jpeg,jpg,png,gif,pdf,doc,docx';
        $mimeTypes = $allowedTypes ?: $defaultTypes;

        $rules = [
            'file',
            'max:' . $maxSize, // Size in KB
            'mimes:' . $mimeTypes
        ];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Common validation rules for multiple file uploads
     */
    public static function multipleFileRules($required = false, $maxSize = 10240, $allowedTypes = null, $maxFiles = 10)
    {
        $rules = [];

        if ($required) {
            $rules['files'] = 'required|array';
        } else {
            $rules['files'] = 'nullable|array';
        }

        $rules['files'] = $rules['files'] . '|max:' . $maxFiles;
        $rules['files.*'] = self::fileRules(true, $maxSize, $allowedTypes);

        return $rules;
    }

    /**
     * Get common error messages
     */
    public static function getCommonMessages()
    {
        return [
            'required' => 'The :attribute field is required.',
            'string' => 'The :attribute must be a string.',
            'email' => 'The :attribute must be a valid email address.',
            'unique' => 'The :attribute has already been taken.',
            'max' => 'The :attribute may not be greater than :max characters.',
            'min' => 'The :attribute must be at least :min characters.',
            'numeric' => 'The :attribute must be a number.',
            'integer' => 'The :attribute must be an integer.',
            'date' => 'The :attribute must be a valid date.',
            'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
            'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
            'file' => 'The :attribute must be a file.',
            'mimes' => 'The :attribute must be a file of type: :values.',
            'regex' => 'The :attribute format is invalid.',
            'exists' => 'The selected :attribute is invalid.'
        ];
    }

    /**
     * Get common field attributes
     */
    public static function getCommonAttributes()
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'work_phone' => 'work phone',
            'contact_phone' => 'contact phone',
            'work_email' => 'work email',
            'contact_email' => 'contact email',
            'pax' => 'passengers',
            'pax_free' => 'free passengers',
            'total_amount' => 'total amount',
            'departure_date' => 'departure date',
            'retirement_date' => 'return date',
            'client_id' => 'client',
            'assigned_user' => 'assigned user'
        ];
    }

    /**
     * Validate business-specific rules for tours
     */
    public static function tourBusinessRules()
    {
        return [
            'departure_date' => self::dateRules(true, 'today', '+2 years'),
            'retirement_date' => array_merge(
                self::dateRules(true, 'departure_date', '+2 years'),
                [new ValidTourDateRange(request('departure_date'))]
            ),
            'pax' => self::passengerRules(true, 1, 500),
            'pax_free' => self::passengerRules(false, 0, 50),
            'total_amount' => self::moneyRules(false, 0, 999999.99),
        ];
    }

    /**
     * Validate business-specific rules for clients
     */
    public static function clientBusinessRules($isUpdate = false, $clientId = null)
    {
        $emailUnique = $isUpdate && $clientId
            ? 'clients,work_email,' . $clientId
            : 'clients,work_email';

        return [
            'first_name' => self::nameRules(true, 191, 2),
            'work_email' => self::emailRules(true, $emailUnique),
            'contact_email' => self::emailRules(false, 'clients,contact_email'),
            'work_phone' => self::phoneRules(true),
            'contact_phone' => self::phoneRules(false),
            'address' => self::addressRules(true, 500, 5)
        ];
    }

    /**
     * Validate business-specific rules for hotels
     */
    public static function hotelBusinessRules($isUpdate = false, $hotelId = null)
    {
        $emailUnique = $isUpdate && $hotelId
            ? 'hotels,work_email,' . $hotelId
            : 'hotels,work_email';

        return [
            'name' => self::businessNameRules(true, 191, 2),
            'work_email' => self::emailRules(true, $emailUnique),
            'work_phone' => self::phoneRules(true),
            'address_first' => self::addressRules(true, 500, 5),
            'city' => self::nameRules(true, 191, 2),
            'country' => self::nameRules(true, 191, 2)
        ];
    }
}