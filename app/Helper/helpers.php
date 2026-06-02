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
            // HTMLPurifier writes its compiled definition cache as .ser
            // files into <cacheDir>/{HTML,CSS,URI}/. If any of those dirs
            // is missing or not writable the purifier dies with a hard
            // error during render — we hit this on /tour/{id}/landingpage
            // after artisan, run as root, had created the dirs as root
            // while Apache (www-data) was unable to write into them.
            // Materialise the tree ourselves and surface failures instead
            // of silencing them with @mkdir.
            $cacheDir = storage_path('app/htmlpurifier');
            foreach ([$cacheDir, $cacheDir . '/HTML', $cacheDir . '/CSS', $cacheDir . '/URI'] as $d) {
                if (!is_dir($d) && !mkdir($d, 0775, true) && !is_dir($d)) {
                    throw new \RuntimeException("Unable to create HTMLPurifier cache directory: $d");
                }
                if (!is_writable($d)) {
                    @chmod($d, 0775);
                    if (!is_writable($d)) {
                        throw new \RuntimeException("HTMLPurifier cache directory not writable: $d");
                    }
                }
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
