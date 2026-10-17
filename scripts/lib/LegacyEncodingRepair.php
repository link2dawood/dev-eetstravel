<?php

class LegacyEncodingRepair
{
    public static function repair(string $value): string
    {
        for ($pass = 0; $pass < 4; $pass++) {
            $chars = mb_str_split($value);
            $result = '';
            for ($i = 0; $i < count($chars); $i++) {
                $first = self::byte($chars[$i]);
                $length = $first >= 0xC2 && $first <= 0xDF ? 2 : ($first >= 0xE0 && $first <= 0xEF ? 3 : ($first >= 0xF0 && $first <= 0xF4 ? 4 : 0));
                $bytes = $length ? chr($first) : '';
                for ($j = 1; $j < $length; $j++) {
                    $next = isset($chars[$i + $j]) ? self::byte($chars[$i + $j]) : -1;
                    if ($next < 0x80 || $next > 0xBF) { $bytes = ''; break; }
                    $bytes .= chr($next);
                }
                if ($bytes !== '' && mb_check_encoding($bytes, 'UTF-8')) {
                    $result .= $bytes;
                    $i += $length - 1;
                } else {
                    $result .= $chars[$i];
                }
            }
            if ($result === $value) break;
            $value = $result;
        }
        return $value;
    }

    private static function byte(string $char): int
    {
        $code = mb_ord($char);
        if ($code <= 255) return $code;
        $byte = mb_convert_encoding($char, 'Windows-1252', 'UTF-8');
        return strlen($byte) === 1 && mb_convert_encoding($byte, 'UTF-8', 'Windows-1252') === $char ? ord($byte) : -1;
    }
}
