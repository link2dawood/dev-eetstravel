/*
 * TMS — application JS entry point.
 * Compiled by Laravel Mix into public/js/app.js.
 *
 * Phase 2 of the Bootstrap → Tailwind migration introduces Alpine.js as the
 * behavior layer for the new widget library. Alpine is loaded globally so
 * Blade x-components (in resources/views/components/ui/*.blade.php) can use
 * `x-data`, `x-show`, `x-on:click` directives directly in markup.
 *
 * IMPORTANT: This file is additive. The existing jQuery/Vue islands across
 * the app continue to work unchanged. Alpine and jQuery coexist fine —
 * Alpine reads `x-*` attributes that jQuery doesn't touch.
 */

require('./bootstrap');

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

// Alpine plugins:
// - focus: traps focus inside modals, restores it on close
Alpine.plugin(focus);

// Expose Alpine on `window` so any inline <script> blocks in legacy Blade
// pages can call `Alpine.store(...)` etc. while we migrate.
window.Alpine = Alpine;

Alpine.start();
