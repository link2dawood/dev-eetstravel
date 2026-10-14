<?php

namespace App\Helper;

/**
 * Removes active content from rich-text HTML that is later printed unescaped
 * (comments, email templates): <script>, <iframe>, <object>, <embed>, event
 * handler attributes (onclick=, onload=...) and javascript: links.
 * Formatting, tables, images and inline styles are kept, so email templates
 * still look the same. Also drops CKEditor's internal "cke_actscrpt" loader,
 * which was sometimes saved along with the text.
 */
class HtmlSanitizer
{
    public static function clean($html)
    {
        if ($html === null || $html === '') {
            return $html;
        }

        $html = (string) $html;
        $html = preg_replace('#<(script|iframe|object|embed|applet)\b[^>]*>.*?</\1\s*>#is', '', $html);
        $html = preg_replace('#<(script|iframe|object|embed|applet|meta|base)\b[^>]*/?>#i', '', $html);
        // on...= event handlers, quoted or not
        $html = preg_replace('#\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html);
        // javascript: / vbscript: in links and sources
        $html = preg_replace('#\b(href|src|action|formaction|xlink:href)\s*=\s*(["\']?)\s*(javascript|vbscript):[^"\'>\s]*\2#i', '$1="#"', $html);

        return trim($html);
    }
}
