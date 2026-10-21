<?php

namespace App\Support;

class SafeHtml
{
    public static function clean($value): string
    {
        static $purifier;
        if (!$purifier) {
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('Cache.DefinitionImpl', null);
            $config->set('HTML.Allowed', 'p,br,b,strong,i,em,u,ul,ol,li,blockquote,span,div,a[href|title],table,thead,tbody,tr,td[colspan|rowspan],th[colspan|rowspan]');
            $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
            $purifier = new \HTMLPurifier($config);
        }
        return $purifier->purify((string) $value);
    }
}
