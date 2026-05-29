const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 | Phase 2 of the Bootstrap → Tailwind migration adds a new Tailwind CSS
 | entry compiled via PostCSS to public/css/tailwind.css. The existing
 | Sass build remains unchanged so the rest of the app keeps working
 | during the per-module migration.
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    // Tailwind entry. The PostCSS pipeline picks up tailwind.config.js
    // and postcss.config.js automatically.
    .postCss('resources/css/tailwind.css', 'public/css', [
        require('tailwindcss'),
        require('autoprefixer'),
    ])
    .sourceMaps();
