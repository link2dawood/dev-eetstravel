<?php
namespace App\Helper;

class CommentHtml
{
    public static function clean($html): string
    {
        static $purifier;
        if (!$purifier) {
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('HTML.Allowed', 'p,br,b,strong,i,em,u,ul,ol,li,blockquote,span[style],a[href|title]');
            $config->set('CSS.AllowedProperties', ['color', 'background-color', 'font-weight', 'font-style', 'text-decoration']);
            $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
            $config->set('Cache.DefinitionImpl', null);
            $purifier = new \HTMLPurifier($config);
        }
        return $purifier->purify((string)$html);
    }
}
