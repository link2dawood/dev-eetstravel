<?php

namespace App\Helper;

/**
 * Amount columns are DECIMAL(12,2); show whole amounts as "99" and others as "89.50",
 * so screens and emails look the same as before the columns allowed cents.
 */
trait FormatsDecimalAmounts
{
    protected function formatAmount($value)
    {
        if ($value === null || $value === '') {
            return $value;
        }
        $number = (float) $value;
        return floor($number) == $number ? (int) $number : number_format($number, 2, '.', '');
    }
}
