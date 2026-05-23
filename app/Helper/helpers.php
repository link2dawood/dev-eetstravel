<?php

/**
 * Global helper functions auto-loaded via composer (see composer.json
 * "files"). Keep this file small — prefer trait methods on specific
 * domains where possible.
 */

if (!function_exists('purify_html')) {
    /**
     * Sanitise user-provided HTML for safe rendering with {!! ... !!}.
     *
     * Strips <script>, on* event attributes, javascript: URLs, and all
     * non-whitelisted tags/attributes. Allows the basics needed for
     * package descriptions and other rich-text fields (p, br, b, i, em,
     * strong, ul/ol/li, a[href], h1-h6, span, div).
     *
     * Uses ezyang/htmlpurifier (already a composer dep). Config + the
     * filesystem cache directory under storage/app/htmlpurifier are
     * created on first call.
     *
     * @param string|null $html Raw user input.
     * @return string           Sanitised HTML safe for {!! ... !!}.
     */
    function purify_html(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        static $purifier = null;

        if ($purifier === null) {
            $cacheDir = storage_path('app/htmlpurifier');
            if (!is_dir($cacheDir)) {
                @mkdir($cacheDir, 0775, true);
            }

            $config = \HTMLPurifier_Config::createDefault();
            $config->set('Cache.SerializerPath', $cacheDir);
            $config->set('HTML.Allowed',
                'p,br,b,strong,i,em,u,'
                . 'ul,ol,li,'
                . 'a[href|title|target],'
                . 'h1,h2,h3,h4,h5,h6,'
                . 'span,div,'
                . 'blockquote,code,pre'
            );
            $config->set('Attr.AllowedFrameTargets', ['_blank']);
            $config->set('AutoFormat.AutoParagraph', false);
            $config->set('AutoFormat.RemoveEmpty', true);
            $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);

            $purifier = new \HTMLPurifier($config);
        }

        return $purifier->purify($html);
    }
}
