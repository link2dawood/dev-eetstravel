<?php

namespace App\Helper;

class ActivityDescription
{
    public static function plainText(?string $description): string
    {
        $text = $description ?? '';
        $text = preg_replace('#<(script|style)\b[^>]*>.*?</\1\s*>#is', '', $text);
        $text = preg_replace('#</?(?:p|div|li|ul|ol|h[1-6])\b[^>]*>|<br\b[^>]*>#i', ' ', $text);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $text));
    }
}
