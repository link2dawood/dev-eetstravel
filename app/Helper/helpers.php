<?php

if (!function_exists('display_date')) {
    /**
     * Format a date for display using the single app-wide format (DD-MM-YYYY).
     *
     * @param  mixed  $value     date string, DateTimeInterface or null
     * @param  string $fallback  shown when the value is empty or unparsable
     * @return string
     */
    function display_date($value, $fallback = '-')
    {
        if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return $fallback;
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d-m-Y');
        } catch (\Exception $e) {
            return $fallback;
        }
    }
}
