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
 */

mix.setPublicPath(process.env.MIX_OUTPUT_PATH || 'public')
    .js('resources/js/app.js', 'js')
    .sass('resources/sass/app.scss', 'css')
    .sourceMaps();
