/** @type {import('tailwindcss').Config} */
//
// TMS design system tokens.
// Used by the resources/views/components/ui/* widget library.
// Phase 2 of the Bootstrap → Tailwind migration (UI_MIGRATION_AUDIT.md).
//
// Principles (from the migration brief):
// - Minimal: lots of whitespace, no decorative gradients, no shadow-on-shadow
// - Neutral palette: slate base + one accent (teal-600)
// - Typography: one sans-serif family (Inter), 6 sizes, 3 weights
// - Density: comfortable for data-heavy office work
// - 8px baseline grid
//
module.exports = {
    // Tailwind v3 JIT scans these paths for class names and only emits CSS for
    // classes it actually finds. Keep this list exhaustive — anything new
    // that uses utility classes (Blade view, JS that injects classes, etc.)
    // must be listed here or its classes will be purged out of the build.
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/Http/Controllers/**/*.php', // some controllers return inline HTML
        './app/Helper/**/*.php',
        // Composer-vendored Lucide icons render through the Blade Icons
        // service provider — including their templates ensures the SVG class
        // attributes (h-4 w-4 etc.) survive purge.
        './vendor/blade-ui-kit/blade-icons/resources/**/*.blade.php',
    ],

    // Layered so utility classes win over Bootstrap when both are loaded.
    // Bootstrap stays loaded during the page-by-page migration; Tailwind's
    // utilities are loaded *after* Bootstrap in the layout so they take
    // precedence on conflicts (e.g. `text-center`, `d-flex` vs `flex`).
    important: false,

    // No dark-mode in Phase 2; revisit after migration completes.
    darkMode: 'class',

    // PREFLIGHT IS OFF during the Bootstrap → Tailwind migration.
    // ----------------------------------------------------------------------
    // Tailwind's Preflight is an opinionated element-default reset (zeros
    // borders globally, strips bullets from <ul>/<ol>, collapses <h1>-<h6>
    // to body text, promotes <img>/<svg> to display:block, etc.). Tabler
    // and Bootstrap rely on browser/element defaults that Preflight wipes,
    // so loading Preflight on top of Tabler hides the sidebar menu and
    // breaks list/heading rendering across the staff layout.
    //
    // While we are still page-by-page migrating Blade views from Bootstrap
    // to Tailwind, Tabler owns the base reset. Tailwind contributes
    // *utilities only* (`bg-primary-600`, `grid`, `flex`, `gap-4`, …),
    // which are unaffected by this switch.
    //
    // Re-enable in Phase 4 once the last Bootstrap-rendered page is gone.
    // See SIDEBAR_DIAGNOSIS.md for the full root-cause analysis.
    corePlugins: {
        preflight: false,

        // Disable Tailwind's `visibility` plugin entirely. It emits
        // `.collapse { visibility: collapse }` regardless of the
        // theme.visibility override, and that class name collides with
        // Bootstrap's collapse component (the staff sidebar's
        // `<div class="collapse navbar-collapse">`). Tailwind wins on
        // source order because tailwind.css is loaded AFTER Tabler, so
        // the sidebar menu becomes invisible while still occupying space.
        //
        // We lose Tailwind's `.visible` and `.invisible` utilities; if
        // those become necessary, re-introduce them as named custom
        // classes in resources/css/tailwind.css. So far no widget uses
        // them.
        //
        // Re-enable in Phase 4 once Bootstrap is gone.
        visibility: false,
    },

    theme: {
        // Override the default font stack — we ship Inter for UI and JetBrains
        // Mono for codes (invoice numbers, tour codes, log output).
        fontFamily: {
            sans: [
                'Inter',
                'system-ui',
                '-apple-system',
                '"Segoe UI"',
                'Roboto',
                '"Helvetica Neue"',
                'Arial',
                'sans-serif',
            ],
            mono: [
                '"JetBrains Mono"',
                '"SFMono-Regular"',
                'Consolas',
                '"Liberation Mono"',
                'Menlo',
                'monospace',
            ],
        },

        // Six sizes only. text-base is the default body size; reach for the
        // others sparingly. Letter-spacing is tuned for Inter at each size.
        fontSize: {
            xs:   ['0.75rem', { lineHeight: '1rem',     letterSpacing: '0' }],          // 12 / 16
            sm:   ['0.875rem', { lineHeight: '1.25rem', letterSpacing: '0' }],          // 14 / 20  (body, table cell, input)
            base: ['1rem',     { lineHeight: '1.5rem',  letterSpacing: '-0.005em' }],   // 16 / 24  (default body)
            lg:   ['1.25rem',  { lineHeight: '1.75rem', letterSpacing: '-0.01em' }],    // 20 / 28  (section heading)
            xl:   ['1.5rem',   { lineHeight: '2rem',    letterSpacing: '-0.015em' }],   // 24 / 32  (page title)
            '2xl':['1.875rem', { lineHeight: '2.25rem', letterSpacing: '-0.02em' }],    // 30 / 36  (display)
        },

        // Three weights only — hierarchy through size and weight, not color.
        fontWeight: {
            normal:   '400',
            medium:   '500',
            semibold: '600',
        },

        // 8px baseline grid. Tailwind already follows this for most steps
        // (0.5rem = 8px, 1rem = 16px, …), so we just expose the standard
        // scale and one extra small step for fine-grained corner spacing.
        // Width/height/padding/margin all share this scale.
        extend: {
            spacing: {
                '0.5': '0.125rem',  // 2 px  — borders, icon offsets
                '1':   '0.25rem',   // 4 px  — gap between icon + label
                '1.5': '0.375rem',  // 6 px
                '2':   '0.5rem',    // 8 px  — primary baseline
                '2.5': '0.625rem',  // 10 px
                '3':   '0.75rem',   // 12 px
                '4':   '1rem',      // 16 px
                '5':   '1.25rem',   // 20 px
                '6':   '1.5rem',    // 24 px
                '8':   '2rem',      // 32 px
                '10':  '2.5rem',    // 40 px
                '12':  '3rem',      // 48 px
                '16':  '4rem',      // 64 px
                // Component heights (used by Button, Input, Select)
                '9':   '2.25rem',   // 36 px — md form field / md button
                '7':   '1.75rem',   // 28 px — badge / chip
            },

            // Color tokens. Use these exclusively — no hex literals in widgets.
            //
            // Neutral: Tailwind's slate (cool gray). Used for text, borders,
            //     backgrounds, surfaces. Same scale exposed to widgets.
            //
            // Accent: teal-600 (#0d9488). One brand color for primary CTAs,
            //     active states, focus rings, links.
            //
            // Semantic: standard Tailwind values mapped to product meaning.
            //     Don't introduce new shades — use the 50/600/700 triplet.
            colors: {
                // Override `primary` so any old Bootstrap `text-primary` /
                // `bg-primary` rules that survive into Phase 3 still render
                // in brand color (not Bootstrap blue) until they're cleaned.
                primary: {
                    50:  '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#14b8a6',
                    600: '#0d9488',  // accent
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a',
                    950: '#042f2e',
                },
                // Semantic aliases. Each has a 50 (subtle bg), 600 (default),
                // 700 (hover / active). Three steps is enough.
                success: {
                    50:  '#ecfdf5',
                    600: '#059669',
                    700: '#047857',
                },
                warning: {
                    50:  '#fffbeb',
                    600: '#d97706',
                    700: '#b45309',
                },
                danger: {
                    50:  '#fef2f2',
                    600: '#dc2626',
                    700: '#b91c1c',
                },
                info: {
                    50:  '#eff6ff',
                    600: '#2563eb',
                    700: '#1d4ed8',
                },
            },

            // Single ring color for focus states. Widgets use `focus-visible`
            // not `focus`, so this never appears on click-based focus.
            ringColor: {
                DEFAULT: '#0d9488', // primary-600
            },

            // Containers: data-dense office UI, not marketing-page width.
            container: {
                center: true,
                padding: {
                    DEFAULT: '1rem',
                    sm: '1.5rem',
                    lg: '2rem',
                },
                screens: {
                    sm: '640px',
                    md: '768px',
                    lg: '1024px',
                    xl: '1280px',
                    '2xl': '1440px', // narrower than Tailwind default (1536px)
                },
            },

            // Border radius — subtle, never decorative.
            borderRadius: {
                'sm': '0.25rem',  // 4 px — input, badge
                DEFAULT: '0.375rem', // 6 px — button, card
                'md': '0.5rem',  // 8 px — modal
                'lg': '0.75rem', // 12 px — dashboard cards
            },

            // Shadows — minimal. One single low shadow for floating surfaces.
            // No double-layered shadows.
            boxShadow: {
                'none':    'none',
                'subtle':  '0 1px 2px 0 rgba(15, 23, 42, 0.04)',
                'card':    '0 1px 3px 0 rgba(15, 23, 42, 0.06), 0 1px 2px -1px rgba(15, 23, 42, 0.04)',
                'overlay': '0 10px 25px -5px rgba(15, 23, 42, 0.10), 0 8px 10px -6px rgba(15, 23, 42, 0.06)',
            },

            // Animation timings — short and uniform.
            transitionDuration: {
                DEFAULT: '150ms',
            },
        },

    },

    plugins: [
        require('@tailwindcss/forms')({
            // The forms plugin re-styles native form elements. We strategy:
            // 'class' so it only applies where we opt in via `form-input`,
            // letting Bootstrap's `form-control` continue to work on the
            // pages we haven't migrated yet.
            strategy: 'class',
        }),
    ],
};
