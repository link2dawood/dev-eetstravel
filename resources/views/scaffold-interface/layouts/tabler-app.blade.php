<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ Auth::user()->id }}">
    @endauth

    <title>@yield('title', 'TMS - Tour Management System')</title>

    <!-- Preload icon fonts so icons don't render as empty boxes first -->
    <link rel="preload" href="{{ asset('tabler/css/fonts/tabler-icons.woff2') }}?v2.47.0" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('css/font-awesome-4.7.0/fonts/fontawesome-webfont.woff2') }}?v=4.7.0" as="font" type="font/woff2" crossorigin>

    <!-- CSS files -->
    <link href="{{ asset('tabler/css/tabler.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('tabler/css/tabler-icons.min.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{asset('css/jquery.toast.css')}}">
    <link rel="stylesheet" href="{{asset('css/fileinput.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/magnific.css')}}">
    <link href="{{asset('css/select2.min.css')}}" rel="stylesheet"/>
    <link href="{{asset('css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet"/>
    <link href="{{asset('css/bootstrap-datepicker.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('css/responsive-global.css')}}" rel="stylesheet" type="text/css"/>
    
    <!-- Modern UI Enhancements -->
    <link href="{{asset('css/modern-forms.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('css/modern-tables.css')}}" rel="stylesheet" type="text/css"/>

    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
        .navbar-brand-image {
            height: 2rem;
        }
        .nav-item.active {
            background-color: rgba(32, 107, 196, 0.06);
            border-right: 2px solid #206bc4;
        }
        .loadingoverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 9999;
            display: none;
        }
        .loadingoverlay_fontawesome {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3rem;
            color: #fff;
        }
        .protect_loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 9999;
        }
        .protect_loader .loadingoverlay_fontawesome {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3rem;
            color: #fff;
        }
        .hidden {
            display: none;
        }

        /* Legacy AdminLTE Component Compatibility */
        .box {
            background: #ffffff;
            border: 1px solid var(--tblr-border-color, #e5e7eb);
            border-radius: var(--tblr-border-radius, 6px);
            margin-bottom: 1.5rem;
            box-shadow: var(--tblr-box-shadow, rgba(0,0,0,0.04) 0 2px 4px 0);
        }

        .box-header {
            background: #ffffff;
            border-bottom: 1px solid var(--tblr-border-color, #e5e7eb);
            padding: 1rem 1.5rem;
            border-radius: var(--tblr-border-radius, 6px) var(--tblr-border-radius, 6px) 0 0;
        }

        .box-header h3,
        .box-header .box-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--tblr-body-color, #1f2937);
        }

        .box-body {
            padding: 1.5rem;
        }

        .box-footer {
            background: #f9fafb;
            border-top: 1px solid var(--tblr-border-color, #e5e7eb);
            padding: 1rem 1.5rem;
            border-radius: 0 0 var(--tblr-border-radius, 6px) var(--tblr-border-radius, 6px);
        }

        .box-tools {
            float: right;
            margin-top: -0.25rem;
        }

        .box-tools .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .btn-box-tool {
            background: transparent;
            border: none;
            color: var(--tblr-secondary-color, #6b7280);
            cursor: pointer;
            padding: 0.25rem 0.5rem;
        }

        .btn-box-tool:hover {
            color: var(--tblr-primary, #066fd1);
        }

        /* Content Header */
        .content-header {
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
        }

        .content-header h1 {
            margin: 0 0 0.5rem 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--tblr-body-color, #1f2937);
        }

        .content-header h1 small {
            font-size: 0.875rem;
            font-weight: 400;
            color: var(--tblr-secondary-color, #6b7280);
            margin-left: 0.5rem;
        }

        /* Breadcrumb */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            list-style: none;
            display: flex;
            flex-wrap: wrap;
        }

        .breadcrumb li {
            display: inline-block;
        }

        .breadcrumb li:not(:last-child)::after {
            content: "/";
            margin: 0 0.5rem;
            color: var(--tblr-secondary-color, #6b7280);
        }

        /* Separator comes from ::after above; drop Tabler's ::before so it isn't doubled */
        .breadcrumb .breadcrumb-item + .breadcrumb-item::before,
        .content-header > .breadcrumb > li + li::before {
            content: none !important;
            display: none !important;
        }
        .breadcrumb .breadcrumb-item + .breadcrumb-item {
            padding-left: 0;
        }

        /* Standard action buttons: filled colour, white icon, same size everywhere.
           view = orange, edit = blue, delete = red, copy = green.
           !important + the repeated class beat older per-section styles (e.g. tinted .monday-action-btn). */
        body .dash-act.dash-act.dash-act {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 40px !important;
            height: 30px !important;
            min-width: 40px;
            padding: 0 !important;
            margin: 0;
            border: 0 !important;
            border-radius: 7px !important;
            color: #ffffff !important;
            box-shadow: none !important;
            line-height: 1 !important;
            cursor: pointer;
            transition: filter .15s ease, box-shadow .15s ease;
        }
        body .dash-act.dash-act.dash-act i,
        body .dash-act.dash-act.dash-act .ti,
        body .dash-act.dash-act.dash-act .fa { font-size: 17px !important; color: #ffffff !important; line-height: 1; margin: 0 !important; }
        body .dash-act.dash-act.dash-act svg { width: 17px !important; height: 17px !important; color: #ffffff !important; stroke: #ffffff !important; margin: 0 !important; }
        body .dash-act.dash-act.dash-act.dash-act-view   { background-color: #f59f00 !important; }
        body .dash-act.dash-act.dash-act.dash-act-edit   { background-color: #0b6bcb !important; }
        body .dash-act.dash-act.dash-act.dash-act-delete { background-color: #d63939 !important; }
        body .dash-act.dash-act.dash-act.dash-act-copy   { background-color: #2fb344 !important; }
        body .dash-act.dash-act.dash-act:hover,
        body .dash-act.dash-act.dash-act:focus,
        body .dash-act.dash-act.dash-act:active {
            color: #ffffff !important;
            filter: brightness(.92);
            outline: none;
        }
        body .dash-act.dash-act.dash-act:focus-visible { box-shadow: 0 0 0 3px rgba(11, 107, 203, .25) !important; }
        body .dash-act.dash-act.dash-act:hover i, body .dash-act.dash-act.dash-act:focus i, body .dash-act.dash-act.dash-act:active i,
        body .dash-act.dash-act.dash-act:hover svg, body .dash-act.dash-act.dash-act:focus svg, body .dash-act.dash-act.dash-act:active svg { color: #ffffff !important; stroke: #ffffff !important; }
        body .dash-act.dash-act.dash-act.dash-act-view:hover, body .dash-act.dash-act.dash-act.dash-act-view:focus, body .dash-act.dash-act.dash-act.dash-act-view:active       { background-color: #f59f00 !important; }
        body .dash-act.dash-act.dash-act.dash-act-edit:hover, body .dash-act.dash-act.dash-act.dash-act-edit:focus, body .dash-act.dash-act.dash-act.dash-act-edit:active       { background-color: #0b6bcb !important; }
        body .dash-act.dash-act.dash-act.dash-act-delete:hover, body .dash-act.dash-act.dash-act.dash-act-delete:focus, body .dash-act.dash-act.dash-act.dash-act-delete:active { background-color: #d63939 !important; }
        body .dash-act.dash-act.dash-act.dash-act-copy:hover, body .dash-act.dash-act.dash-act.dash-act-copy:focus, body .dash-act.dash-act.dash-act.dash-act-copy:active       { background-color: #2fb344 !important; }

        /* Open dropdown toggles keep dark, readable text */
        .btn-secondary.show,
        .btn-secondary:active,
        .btn-secondary.dropdown-toggle.show,
        .btn-secondary.dropdown-toggle:focus {
            color: #1f2937 !important;
            background-color: #cbd5e1 !important;
            border-color: #94a3b8 !important;
        }

        .breadcrumb li a {
            color: var(--tblr-link-color, #066fd1);
            text-decoration: none;
        }

        .breadcrumb li a:hover {
            color: var(--tblr-link-hover-color, #0559a7);
            text-decoration: underline;
        }

        .breadcrumb li.active {
            color: var(--tblr-secondary-color, #6b7280);
        }

        /* Calendar Widget Specific */
        .calendar-compact {
            margin-bottom: 1.5rem;
        }

        /* Direct Chat & Dashboard Widgets */
        .direct-chat,
        .dashboard-widget-chat {
            background: #ffffff;
            border: 1px solid var(--tblr-border-color, #e5e7eb);
            border-radius: var(--tblr-border-radius, 6px);
            margin-bottom: 1.5rem;
        }

        /* Fix for Container Fluid */
        .container-fluid {
            width: 100%;
            padding-right: 1rem;
            padding-left: 1rem;
            margin-right: auto;
            margin-left: auto;
        }

        /* Color Classes */
        .bg-primary {
            background-color: var(--tblr-primary, #066fd1) !important;
            color: #ffffff;
        }

        .bg-success {
            background-color: var(--tblr-success, #2fb344) !important;
            color: #ffffff;
        }

        .bg-info {
            background-color: var(--tblr-info, #4299e1) !important;
            color: #ffffff;
        }

        .bg-warning {
            background-color: var(--tblr-warning, #f59f00) !important;
            color: #ffffff;
        }

        .bg-danger {
            background-color: var(--tblr-danger, #d63939) !important;
            color: #ffffff;
        }

        .text-primary {
            color: var(--tblr-primary, #066fd1) !important;
        }

        .text-success {
            color: var(--tblr-success, #2fb344) !important;
        }

        .text-info {
            color: var(--tblr-info, #4299e1) !important;
        }

        .text-warning {
            color: var(--tblr-warning, #f59f00) !important;
        }

        .text-danger {
            color: var(--tblr-danger, #d63939) !important;
        }

        /* Button Styles */
        .btn-primary {
            background-color: var(--tblr-primary, #066fd1);
            border-color: var(--tblr-primary, #066fd1);
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #0559a7;
            border-color: #0559a7;
        }

        .btn-success {
            background-color: var(--tblr-success, #2fb344);
            border-color: var(--tblr-success, #2fb344);
            color: #ffffff;
        }

        .btn-info {
            background-color: var(--tblr-info, #4299e1);
            border-color: var(--tblr-info, #4299e1);
            color: #ffffff;
        }

        .btn-warning {
            background-color: var(--tblr-warning, #f59f00);
            border-color: var(--tblr-warning, #f59f00);
            color: #ffffff;
        }

        .btn-danger {
            background-color: var(--tblr-danger, #d63939);
            border-color: var(--tblr-danger, #d63939);
            color: #ffffff;
        }

        .create-action-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center;
            gap: 0.45rem;
            line-height: 1.2;
        }

        .create-action-icon {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 1rem;
            height: 1rem;
            min-width: 1rem;
            line-height: 1 !important;
            font-size: 1.15rem;
            font-weight: 700;
            transform: none !important;
            rotate: 0deg !important;
            margin: 0 !important;
            flex: 0 0 auto;
        }

        .create-action-btn .fa,
        .create-action-btn .icon,
        .create-action-btn svg {
            transform: none !important;
            rotate: 0deg !important;
            margin: 0 !important;
        }
        /* Alert Styles */
        .alert-success {
            background-color: rgba(47, 179, 68, 0.1);
            border-color: var(--tblr-success, #2fb344);
            color: var(--tblr-success, #2fb344);
        }

        .alert-info {
            background-color: rgba(66, 153, 225, 0.1);
            border-color: var(--tblr-info, #4299e1);
            color: var(--tblr-info, #4299e1);
        }

        .alert-warning {
            background-color: rgba(245, 159, 0, 0.1);
            border-color: var(--tblr-warning, #f59f00);
            color: var(--tblr-warning, #f59f00);
        }

        .alert-danger {
            background-color: rgba(214, 57, 57, 0.1);
            border-color: var(--tblr-danger, #d63939);
            color: var(--tblr-danger, #d63939);
        }

        /* Label/Badge Styles */
        .label {
            display: inline-block;
            padding: 0.25em 0.5em;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .label-primary {
            background-color: var(--tblr-primary, #066fd1);
            color: #ffffff;
        }

        .label-success {
            background-color: var(--tblr-success, #2fb344);
            color: #ffffff;
        }

        .label-info {
            background-color: var(--tblr-info, #4299e1);
            color: #ffffff;
        }

        .label-warning {
            background-color: var(--tblr-warning, #f59f00);
            color: #ffffff;
        }

        .label-danger {
            background-color: var(--tblr-danger, #d63939);
            color: #ffffff;
        }

        /* Sidebar Styling */
        .navbar-vertical {
            background: #1e293b;
            box-shadow: 0 0 2rem 0 rgba(0, 0, 0, .1);
        }

        .navbar-vertical .navbar-brand {
            color: #ffffff;
            padding: 1.5rem 1rem;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .navbar-vertical .navbar-brand-text {
            color: #ffffff;
        }

        .navbar-vertical .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            margin: 0.125rem 0.5rem;
            transition: all 0.2s;
        }

        .navbar-vertical .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #ffffff;
        }

        .navbar-vertical .nav-item.active > .nav-link,
        .navbar-vertical .nav-link.active {
            background-color: rgba(6, 111, 209, 0.15);
            color: #ffffff;
            font-weight: 500;
        }

        .navbar-vertical .nav-link-icon {
            margin-right: 0.5rem;
            width: 1.5rem;
            height: 1.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .navbar-vertical .nav-link-icon i,
        .navbar-vertical .nav-link-icon .icon {
            font-size: 1.25rem;
            width: 1.25rem;
            height: 1.25rem;
            color: inherit;
        }

        .navbar-vertical .nav-link-title {
            flex: 1;
        }

        .navbar-vertical .dropdown-menu {
            background: #0f172a;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin: 0;
            padding: 0.5rem 0;
        }

        .navbar-vertical .dropdown-item {
            color: rgba(255, 255, 255, 0.7);
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            transition: all 0.2s;
        }

        .navbar-vertical .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #ffffff;
        }

        .navbar-vertical .dropdown-item.active {
            background-color: rgba(6, 111, 209, 0.15);
            color: #ffffff;
        }

        .navbar-vertical .dropdown-item i,
        .navbar-vertical .dropdown-item .icon {
            color: inherit;
            opacity: 0.7;
        }

        .navbar-vertical .hr-text {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem 1rem 0.5rem;
            margin: 0.5rem 0;
        }

        .navbar-vertical .dropdown-toggle::after {
            margin-left: auto;
            opacity: 0.5;
        }

        /* Icon Fixes */
        .ti, [class^="ti-"], [class*=" ti-"] {
            font-family: 'tabler-icons' !important;
            speak: none;
            font-style: normal;
            font-weight: normal;
            font-variant: normal;
            text-transform: none;
            line-height: 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            display: inline-block;
            vertical-align: middle;
        }

        .icon {
            width: 1.25rem;
            height: 1.25rem;
            font-size: 1.25rem;
            vertical-align: middle;
        }

        /* Navbar Header Icons */
        .navbar-light .nav-link .icon,
        .navbar-light .nav-link i {
            color: var(--tblr-body-color, #1f2937);
            font-size: 1.25rem;
        }

        /* Badge Styles */
        .badge.bg-red {
            background-color: var(--tblr-danger, #d63939) !important;
            color: #ffffff !important;
        }

        /* Dropdown Menu Improvements */
        .dropdown-menu-arrow {
            margin-top: 0.5rem;
        }

        .dropdown-item-icon {
            margin-right: 0.5rem;
            width: 1.25rem;
            display: inline-block;
        }

        /* Active State for Navigation */
        .navbar-vertical .nav-item.active .nav-link-icon {
            color: var(--tblr-primary, #066fd1);
        }

        /* FontAwesome Fallback Support */
        .fa, .fas, .far, .fal, .fab {
            font-family: 'FontAwesome', 'Font Awesome 5 Free', 'Font Awesome 5 Brands', 'tabler-icons' !important;
        }

        /* Hover legends start hidden; utils.js fades them in, so they never widen the page */
        #legend_help, #legend_help_quotation, #legend_help_guest_list { display: none; }

        /* Ensure Tabler Icons Load */
        @font-face {
            font-family: 'tabler-icons';
            src: url('/tabler/css/fonts/tabler-icons.eot?v2.47.0');
            src: url('/tabler/css/fonts/tabler-icons.eot?#iefix-v2.47.0') format('embedded-opentype'),
                 url('/tabler/css/fonts/tabler-icons.woff2?v2.47.0') format('woff2'),
                 url('/tabler/css/fonts/tabler-icons.woff?') format('woff'),
                 url('/tabler/css/fonts/tabler-icons.ttf?v2.47.0') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        /* fa-* icons render from Font Awesome 4.7 (loaded in <head>); no tabler remapping needed */

        /* Global readable form fields */
        input.form-control,
        select.form-control,
        textarea.form-control,
        .form-control,
        .form-select,
        .input-group .form-control,
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            background-color: #ffffff !important;
            color: #111827 !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            box-shadow: none !important;
        }

        input.form-control,
        select.form-control,
        textarea.form-control,
        .form-control,
        .form-select {
            min-height: 38px;
        }

        textarea.form-control {
            min-height: 96px;
        }

        .form-control:focus,
        .form-select:focus,
        input.form-control:focus,
        select.form-control:focus,
        textarea.form-control:focus,
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default .select2-selection--single:focus {
            background-color: #ffffff !important;
            color: #111827 !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.14) !important;
            outline: 0 !important;
        }

        .form-control::placeholder,
        input.form-control::placeholder,
        textarea.form-control::placeholder {
            color: #6b7280 !important;
            opacity: 1;
        }

        .form-control:disabled,
        .form-control[readonly],
        .form-select:disabled,
        input.form-control:disabled,
        textarea.form-control:disabled {
            background-color: #f8fafc !important;
            color: #475569 !important;
            opacity: 1;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .select2-container--default .select2-selection--multiple .select2-selection__rendered,
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            color: #111827 !important;
        }

        .select2-container--default .select2-results__option {
            color: #111827 !important;
            background-color: #ffffff;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #eff6ff !important;
            color: #0f172a !important;
        }

        label,
        .form-label,
        .control-label {
            color: #111827;
        }

        /* Bootstrap fileinput control cleanup */
        .file-input .file-caption-main {
            display: flex;
            align-items: stretch;
            gap: 0.5rem;
            width: 100%;
            background: #ffffff;
            border: 1px solid #dbe3ef;
            border-radius: 10px;
            padding: 0.35rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .file-input .file-caption {
            flex: 1 1 auto;
            width: auto !important;
            min-width: 0;
            margin: 0 !important;
            border: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        .file-input .file-caption-main .input-group-btn,
        .file-input .file-caption-main .btn-file {
            flex: 0 0 auto;
            width: auto !important;
            white-space: nowrap;
        }

        .file-input .file-caption-name,
        .file-input .file-caption input,
        .file-input .file-caption .form-control {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            height: 42px !important;
            min-height: 42px !important;
            padding: 0.55rem 0.9rem !important;
            border: 1px solid #dbe3ef !important;
            border-radius: 8px !important;
            background: #f8fafc !important;
            color: #1f2937 !important;
            font-size: 0.925rem;
            font-weight: 500;
            line-height: 1.35 !important;
            box-shadow: none !important;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .file-input .file-caption-name .kv-caption-icon,
        .file-input .file-caption-name .file-caption-icon {
            flex: 0 0 auto;
            margin-right: 0.35rem;
        }

        .file-input .file-caption-name:focus,
        .file-input .file-caption input:focus,
        .file-input .file-caption .form-control:focus {
            background: #ffffff !important;
            border-color: #93c5fd !important;
            box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.18) !important;
            outline: 0 !important;
        }

        .file-input .file-caption-name:not([title=""]),
        .file-input .file-caption input:not(:placeholder-shown) {
            background: #ffffff !important;
            border-color: #bfdbfe !important;
            color: #0f172a !important;
        }

        .file-input .input-group-btn,
        .file-input .btn-file {
            display: flex;
            align-items: stretch;
            gap: 0.5rem;
        }

        .file-input .btn {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0.55rem 1rem;
            border-radius: 8px !important;
            border: 1px solid #dbe3ef !important;
            font-size: 0.9rem;
            font-weight: 600;
            line-height: 1;
            box-shadow: none !important;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
        }

        .file-input .btn-file,
        .file-input .btn-primary {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
        }

        .file-input .btn-file:hover,
        .file-input .btn-file:focus,
        .file-input .btn-primary:hover,
        .file-input .btn-primary:focus {
            background: #0b5ed7 !important;
            border-color: #0b5ed7 !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.16) !important;
        }

        .file-input .fileinput-remove-button,
        .file-input .fileinput-cancel-button {
            background: #ffffff !important;
            border-color: #dbe3ef !important;
            color: #334155 !important;
        }

        .file-input .fileinput-remove-button:hover,
        .file-input .fileinput-remove-button:focus {
            background: #fff1f2 !important;
            border-color: #fecdd3 !important;
            color: #be123c !important;
        }

        .file-input .fileinput-cancel-button:hover,
        .file-input .fileinput-cancel-button:focus {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        .file-input .btn-file input[type="file"] {
            cursor: pointer;
        }

        @media (max-width: 576px) {
            .file-input .file-caption-main {
                flex-direction: column;
            }

            .file-input .input-group-btn,
            .file-input .btn-file,
            .file-input .btn {
                width: 100%;
            }
        }

        /* Room type selector */
        .btn_for_select_room_type {
            display: inline-flex !important;
            align-items: center;
            gap: 0.5rem;
            min-height: 42px;
            padding: 0.65rem 1rem !important;
            border-radius: 8px !important;
            background: #16a34a !important;
            border-color: #16a34a !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 8px 18px rgba(22, 163, 74, 0.18);
        }

        .btn_for_select_room_type::after {
            content: "\ea5f";
            font-family: 'tabler-icons';
            font-size: 1rem;
            transition: transform 0.15s ease;
        }

        .btn_for_select_room_type:hover,
        .btn_for_select_room_type:focus {
            background: #15803d !important;
            border-color: #15803d !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.16);
        }

        .btn_for_select_room_type.is-open::after {
            transform: rotate(180deg);
        }

        .list_room_types {
            display: none;
            width: min(360px, 100%);
            max-height: 280px;
            overflow-y: auto;
            margin: 0.5rem 0 0;
            padding: 0.35rem;
            list-style: none;
            background: #ffffff;
            border: 1px solid #dbe3ef;
            border-radius: 10px;
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.16);
            z-index: 40;
        }

        .list_room_types > li.select_room_type {
            display: flex;
            align-items: center;
            min-height: 38px;
            padding: 0.55rem 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            color: #1f2937;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .list_room_types > li.select_room_type + li.select_room_type {
            margin-top: 0.125rem;
        }

        .list_room_types > li.select_room_type label {
            margin: 0;
            cursor: pointer;
            font-weight: 600;
            color: inherit;
        }

        .list_room_types > li.select_room_type:hover,
        .list_room_types > li.select_room_type:focus {
            background: #eff6ff;
            color: #0f4fb8;
        }

        #list_selected_room_types {
            display: grid;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .item_selected_room_type {
            display: grid;
            grid-template-columns: minmax(140px, 1fr) auto auto auto;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.75rem;
            background: #ffffff;
            border: 1px solid #dbe3ef;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .item_selected_room_type .name_room_type {
            font-weight: 700;
            color: #111827;
        }

        .block-price-room,
        .block-qty-room {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        .block-price-room label,
        .block-qty-room label {
            margin: 0;
            color: #475569;
            font-weight: 600;
            white-space: nowrap;
        }

        .item_selected_room_type .count_room_type {
            width: 96px;
            min-height: 36px;
            text-align: center;
        }

        .icon_delete_room_type {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #be123c;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
        }

        .icon_delete_room_type i,
        .icon_delete_room_type .fa {
            color: #be123c !important;
            font-size: 1rem;
            line-height: 1;
        }

        .icon_delete_room_type .fa-close::before,
        .icon_delete_room_type .fa-times::before {
            content: "\eb55" !important;
            font-family: 'tabler-icons' !important;
            display: inline-block;
            color: #be123c !important;
        }

        .icon_delete_room_type:hover {
            background: #ffe4e6;
            border-color: #fda4af;
            color: #9f1239;
            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.1);
        }

        .icon_delete_room_type:hover i,
        .icon_delete_room_type:hover .fa {
            color: #9f1239 !important;
        }

        /* Make old Bootstrap glyphicons visible in Tabler layout */
        .glyphicon {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: 1em;
            height: 1em;
            font-family: 'tabler-icons' !important;
            font-style: normal;
            font-weight: normal;
            line-height: 1;
            color: currentColor;
        }

        /* Codepoints match tabler-icons v2.47 (public/tabler/css/tabler-icons.min.css) */
        .glyphicon-folder-open::before { content: "\faf7"; } /* ti-folder-open */
        .glyphicon-trash::before { content: "\eb41"; }       /* ti-trash */
        .glyphicon-ban-circle::before { content: "\eb55"; }  /* ti-x (Cancel/Remove) */
        .glyphicon-upload::before { content: "\eb47"; }      /* ti-upload */
        .glyphicon-file::before { content: "\eaa4"; }        /* ti-file */
        .glyphicon-remove::before { content: "\eb55"; }      /* ti-x */
        .glyphicon-zoom-in::before { content: "\eb56"; }     /* ti-zoom-in */
        .glyphicon-eye-open::before { content: "\ea9a"; }    /* ti-eye */
        .glyphicon-resize-full::before,
        .glyphicon-fullscreen::before { content: "\ea28"; }
        .glyphicon-resize-small::before,
        .glyphicon-resize-vertical::before { content: "\ea29"; }
        .glyphicon-triangle-left::before { content: "\ea60"; }   /* ti-chevron-left */
        .glyphicon-triangle-right::before { content: "\ea61"; }  /* ti-chevron-right */
        .glyphicon-exclamation-sign::before { content: "\ea05"; } /* ti-alert-circle */

        .file-input .btn .glyphicon,
        .file-input .btn i,
        .file-input .btn .fa {
            margin-right: 0.35rem;
            color: currentColor !important;
            font-size: 1rem;
        }

        .file-input .btn-file .glyphicon,
        .file-input .btn-primary .glyphicon {
            color: #ffffff !important;
        }

        .file-input .fileinput-remove-button .glyphicon,
        .file-input .fileinput-remove-button i {
            color: #be123c !important;
        }

        .file-input .fileinput-cancel-button .glyphicon,
        .file-input .fileinput-cancel-button i {
            color: #334155 !important;
        }

        .file-input .fileinput-remove-button:hover .glyphicon,
        .file-input .fileinput-remove-button:focus .glyphicon {
            color: #9f1239 !important;
        }

        .file-input .fileinput-cancel-button:hover .glyphicon,
        .file-input .fileinput-cancel-button:focus .glyphicon {
            color: #0f172a !important;
        }

        .file-input .kv-file-zoom {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: 42px;
            min-width: 42px;
            height: 42px;
            padding: 0 !important;
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
            border-radius: 8px !important;
        }

        .file-input .kv-file-zoom:hover,
        .file-input .kv-file-zoom:focus {
            background: #0b5ed7 !important;
            border-color: #0b5ed7 !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.16) !important;
        }

        .file-input .kv-file-zoom .glyphicon,
        .file-input .kv-file-zoom i {
            margin-right: 0 !important;
            color: #ffffff !important;
            font-size: 1.05rem;
        }

        .file-zoom-dialog .modal-header .close,
        .kv-fileinput-modal .modal-header .close,
        .file-zoom-dialog .kv-zoom-actions .btn:last-child,
        .kv-fileinput-modal .kv-zoom-actions .btn:last-child {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            padding: 0 !important;
            margin: 0 0 0 auto !important;
            border: 1px solid #fecdd3 !important;
            border-radius: 8px !important;
            background: #fff1f2 !important;
            color: #be123c !important;
            opacity: 1 !important;
            text-shadow: none !important;
            font-size: 0 !important;
            line-height: 1 !important;
            cursor: pointer;
        }

        .file-zoom-dialog .modal-header .close::before,
        .kv-fileinput-modal .modal-header .close::before {
            content: "\eb55";
            font-family: 'tabler-icons';
            font-size: 1.05rem;
            line-height: 1;
        }

        .file-zoom-dialog .modal-header .close:hover,
        .file-zoom-dialog .modal-header .close:focus,
        .kv-fileinput-modal .modal-header .close:hover,
        .kv-fileinput-modal .modal-header .close:focus,
        .file-zoom-dialog .kv-zoom-actions .btn:last-child:hover,
        .file-zoom-dialog .kv-zoom-actions .btn:last-child:focus,
        .kv-fileinput-modal .kv-zoom-actions .btn:last-child:hover,
        .kv-fileinput-modal .kv-zoom-actions .btn:last-child:focus {
            background: #ffe4e6 !important;
            border-color: #fda4af !important;
            color: #9f1239 !important;
            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.1) !important;
        }

        .file-zoom-dialog .kv-zoom-actions .btn,
        .kv-fileinput-modal .kv-zoom-actions .btn {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            padding: 0 !important;
            border-radius: 8px !important;
        }

        .file-zoom-dialog .kv-zoom-actions .glyphicon,
        .kv-fileinput-modal .kv-zoom-actions .glyphicon {
            margin-right: 0 !important;
            color: currentColor !important;
        }

        .app-toast-stack {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1080;
            display: grid;
            gap: 0.75rem;
            width: min(360px, calc(100vw - 2rem));
        }

        .app-toast {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid #dbe3ef;
            box-shadow: 0 18px 44px rgba(15, 23, 42, 0.16);
            color: #0f172a;
            animation: appToastIn 0.18s ease-out;
        }

        .app-toast-success {
            border-left: 4px solid #16a34a;
        }

        .app-toast-error {
            border-left: 4px solid #dc2626;
        }

        .app-toast-warning {
            border-left: 4px solid #f59e0b;
        }

        .app-toast-info {
            border-left: 4px solid #0d6efd;
        }

        .app-toast-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 999px;
            flex: 0 0 auto;
            background: #eff6ff;
            color: #0d6efd;
            font-family: 'tabler-icons';
            line-height: 1;
        }

        .app-toast-success .app-toast-icon {
            background: #dcfce7;
            color: #16a34a;
        }

        .app-toast-error .app-toast-icon {
            background: #fee2e2;
            color: #dc2626;
        }

        .app-toast-warning .app-toast-icon {
            background: #fef3c7;
            color: #d97706;
        }

        .app-toast-title {
            font-weight: 700;
            margin-bottom: 0.125rem;
        }

        .app-toast-message {
            color: #475569;
            font-size: 0.9rem;
            line-height: 1.35;
        }

        .app-confirm-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1070;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(15, 23, 42, 0.34);
        }

        .app-confirm-dialog {
            width: min(420px, 100%);
            padding: 1.25rem;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
        }

        .app-confirm-title {
            margin: 0 0 0.35rem;
            color: #111827;
            font-size: 1.05rem;
            font-weight: 750;
        }

        .app-confirm-message {
            margin: 0;
            color: #475569;
            line-height: 1.45;
        }

        .app-confirm-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.625rem;
            margin-top: 1.25rem;
        }

        @keyframes appToastIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .item_selected_room_type {
                grid-template-columns: 1fr;
                align-items: stretch;
            }

            .block-price-room,
            .block-qty-room {
                justify-content: space-between;
            }

            .item_selected_room_type .count_room_type {
                width: 120px;
            }
        }
    </style>

    @yield('colorpicker-css')
    @yield('post_styles')
    @stack('styles')

    <script type="text/javascript" src="{{asset('js/lib/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/lib/moment.js')}}"></script>
    <script src="https://jsuites.net/v4/jsuites.js"></script>
    <link rel="stylesheet" href="https://jsuites.net/v4/jsuites.css" type="text/css" />
    <script type="text/javascript" src="{{asset('js/jquery.toast.js')}}"></script>
    
    <!-- Axios for HTTP requests -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script src="{{asset('js/vue.js')}}"></script>
    <script src="{{asset('js/piexif.min.js')}}"></script>
    <script src="{{asset('js/purify.min.js')}}"></script>
    <script src="{{asset('js/fileinput.min.js')}}"></script>
</head>
<body>
    <audio src="/new_message.mp3" id="chat_message"></audio>

    <div class="loadingoverlay">
        <div class="spinner-border text-primary loadingoverlay_fontawesome" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="page">
        @auth
            @include('scaffold-interface.layouts.tabler-sidebar')
        @endauth

        <div class="page-wrapper">
            @auth
                @include('scaffold-interface.layouts.tabler-header')
            @endauth

            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    @include('component.session-messages')
                    @yield('content')
                </div>
            </div>

            @include('scaffold-interface.layouts.tabler-footer')
        </div>
    </div>

    <!-- Modal -->
    <div class="modal modal-blur fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content AjaxisModal">
            </div>
        </div>
    </div>

    <div class="protect_loader hidden">
        <div class="spinner-border text-primary loadingoverlay_fontawesome" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Tabler Core (includes Bootstrap 5) -->
    <script src="{{ asset('tabler/js/tabler.min.js') }}"></script>

    <!-- Ensure Bootstrap global is available -->
    <script>
        // Tabler includes Bootstrap 5, expose it globally if not already
        if (typeof bootstrap === 'undefined' && typeof window.bootstrap !== 'undefined') {
            window.bootstrap = window.bootstrap;
        }
        // Create bootstrap namespace if needed
        if (typeof bootstrap === 'undefined') {
            window.bootstrap = {
                Modal: function(element, options) {
                    this.element = element;
                    this.options = options || {};

                    this.show = function() {
                        $(element).modal('show');
                    };

                    this.hide = function() {
                        $(element).modal('hide');
                    };

                    this.toggle = function() {
                        $(element).modal('toggle');
                    };

                    return this;
                }
            };
        }
    </script>

    <!-- Core JS -->
    <script type="text/javascript" src="{{asset('js/lib/moment-with-locales.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('js/select2.min.js') }}"></script>
    <script> var baseURL = "{{ URL::to('/') }}"</script>
    <script type="text/javascript" src="{{URL::asset('js/AjaxisBootstrap.js') }}"></script>
    <script type="text/javascript" src="{{URL::asset('js/scaffold-interface-js/customA.js') }}"></script>
    <script type="text/javascript" src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('js/script.js')}}"></script>
    <script src="{{asset('js/magnific.js')}}"></script>
    
    <!-- Google Maps API (load before google_places.js) -->
    @if(config('google.places.key'))
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('google.places.key') }}&libraries=places"></script>
    @endif
    
    <script src="{{URL::asset('js/google_places.js')}}"></script>
    <script src="{{URL::asset('js/jquery.scrollTo.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/pusher.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/jquery.repeater.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/bootstrap-tables.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/helper.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/onclick-events.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/notifications.js')}}?v={{ filemtime(public_path('js/notifications.js')) }}"></script>
    <script type="text/javascript" src="{{asset('js/ckeditor/ckeditor.js')}}"></script>
    <script src="{{asset('js/ckeditor.js')}}"></script>
    <script src="{{asset('js/icheck.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/cities.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/action-buttons.js') }}?v={{ filemtime(public_path('js/action-buttons.js')) }}"></script>

    @yield('colorpicker-js')
    @stack('scripts')
    @yield('post_scripts')
    @yield('tour_package_script')
    @yield('post_scripts_calendar')

    <script>
        @auth
            var user_email = "{{ Auth::user()->email_login }}";
        @else
            var user_email = "";
        @endauth

        window.appToast = function(message, type, title) {
            type = type || 'info';
            title = title || (type === 'success' ? 'Success' : type === 'error' ? 'Error' : type === 'warning' ? 'Warning' : 'Notice');

            var stack = document.querySelector('.app-toast-stack');
            if (!stack) {
                stack = document.createElement('div');
                stack.className = 'app-toast-stack';
                document.body.appendChild(stack);
            }

            var icons = {
                success: '\uea5e',
                error: '\ueb55',
                warning: '\uea06',
                info: '\uea05'
            };

            var toast = document.createElement('div');
            toast.className = 'app-toast app-toast-' + type;
            toast.innerHTML = '<span class="app-toast-icon">' + (icons[type] || icons.info) + '</span><div><div class="app-toast-title"></div><div class="app-toast-message"></div></div>';
            toast.querySelector('.app-toast-title').textContent = title;
            toast.querySelector('.app-toast-message').textContent = message;
            stack.appendChild(toast);

            setTimeout(function() {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-8px)';
                toast.style.transition = 'opacity 0.18s ease, transform 0.18s ease';
                setTimeout(function() {
                    toast.remove();
                    if (!stack.children.length) {
                        stack.remove();
                    }
                }, 200);
            }, 3200);
        };


        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.appToast !== 'function') {
                return;
            }

            try {
                const queuedToast = sessionStorage.getItem('appToastAfterReload');
                if (queuedToast) {
                    sessionStorage.removeItem('appToastAfterReload');
                    const toast = JSON.parse(queuedToast);
                    setTimeout(function() {
                        window.appToast(toast.message, toast.type || 'info', toast.title);
                    }, 250);
                }
            } catch (error) {}
        });
        @if(session('success') || session('error') || session('warning') || session('info') || session('message'))
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof window.appToast === 'function') {
                    @if(session('success'))
                        window.appToast(@json(session('success')), 'success');
                    @endif
                    @if(session('error'))
                        window.appToast(@json(session('error')), 'error');
                    @endif
                    @if(session('warning'))
                        window.appToast(@json(session('warning')), 'warning');
                    @endif
                    @if(session('info'))
                        window.appToast(@json(session('info')), 'info');
                    @endif
                    @if(session('message'))
                        window.appToast(@json(session('message')), 'info');
                    @endif
                }
            });
        @endif
        window.appConfirm = function(message, options) {
            options = options || {};

            return new Promise(function(resolve) {
                var backdrop = document.createElement('div');
                backdrop.className = 'app-confirm-backdrop';
                backdrop.innerHTML = [
                    '<div class="app-confirm-dialog" role="dialog" aria-modal="true">',
                        '<h3 class="app-confirm-title"></h3>',
                        '<p class="app-confirm-message"></p>',
                        '<div class="app-confirm-actions">',
                            '<button type="button" class="btn btn-secondary app-confirm-cancel"></button>',
                            '<button type="button" class="btn btn-danger app-confirm-ok"></button>',
                        '</div>',
                    '</div>'
                ].join('');

                backdrop.querySelector('.app-confirm-title').textContent = options.title || 'Confirm delete';
                backdrop.querySelector('.app-confirm-message').textContent = message || 'Are you sure?';
                backdrop.querySelector('.app-confirm-cancel').textContent = options.cancelText || 'Cancel';
                backdrop.querySelector('.app-confirm-ok').textContent = options.confirmText || 'Delete';
                document.body.appendChild(backdrop);

                function close(value) {
                    backdrop.remove();
                    resolve(value);
                }

                backdrop.querySelector('.app-confirm-cancel').addEventListener('click', function() {
                    close(false);
                });

                backdrop.querySelector('.app-confirm-ok').addEventListener('click', function() {
                    close(true);
                });

                backdrop.addEventListener('click', function(event) {
                    if (event.target === backdrop) {
                        close(false);
                    }
                });

                document.addEventListener('keydown', function escapeHandler(event) {
                    if (event.key === 'Escape') {
                        document.removeEventListener('keydown', escapeHandler);
                        close(false);
                    }
                });
            });
        };

        document.addEventListener('click', function (event) {
            const fileZoomClose = event.target.closest('.file-zoom-dialog [data-dismiss="modal"], .file-zoom-dialog .close, .file-zoom-dialog .kv-zoom-actions .btn:last-child, .kv-fileinput-modal [data-dismiss="modal"], .kv-fileinput-modal .close, .kv-fileinput-modal .kv-zoom-actions .btn:last-child');

            if (fileZoomClose) {
                event.preventDefault();
                event.stopPropagation();

                const modal = fileZoomClose.closest('.modal') || document.querySelector('.file-zoom-dialog.show, .file-zoom-dialog.in, .kv-fileinput-modal.show, .kv-fileinput-modal.in');

                if (modal && window.bootstrap && window.bootstrap.Modal) {
                    try {
                        const instance = window.bootstrap.Modal.getInstance(modal) || new window.bootstrap.Modal(modal);
                        instance.hide();
                    } catch (error) {
                        modal.classList.remove('show', 'in');
                        modal.style.display = 'none';
                    }
                }

                if (modal && window.jQuery && jQuery.fn.modal) {
                    jQuery(modal).modal('hide');
                }

                if (modal) {
                    modal.classList.remove('show');
                    modal.classList.remove('in');
                    modal.setAttribute('aria-hidden', 'true');
                    modal.removeAttribute('aria-modal');
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                        backdrop.remove();
                    });
                }

                return;
            }

            const roomButton = event.target.closest('.btn_for_select_room_type');

            if (roomButton) {
                const group = roomButton.closest('.form-group') || document;
                const list = group.querySelector('.list_room_types');
                const isOpen = list && window.getComputedStyle(list).display !== 'none';
                roomButton.classList.toggle('is-open', !isOpen);
                return;
            }

            if (event.target.closest('.list_room_types')) {
                document.querySelectorAll('.btn_for_select_room_type.is-open').forEach(function (button) {
                    button.classList.remove('is-open');
                });
                return;
            }

            document.querySelectorAll('.list_room_types').forEach(function (list) {
                if (window.getComputedStyle(list).display !== 'none') {
                    if (window.jQuery) {
                        jQuery(list).stop(true, true).slideUp(80);
                    } else {
                        list.style.display = 'none';
                    }
                }
            });

            document.querySelectorAll('.btn_for_select_room_type.is-open').forEach(function (button) {
                button.classList.remove('is-open');
            });
        }, true);

        const multiSelectWithoutCtrl = ( elemSelector ) => {
            let options = [].slice.call(document.querySelectorAll(`${elemSelector} option`));
            options.forEach(function (element) {
                element.addEventListener("mousedown",
                    function (e) {
                        e.preventDefault();
                        element.parentElement.focus();
                        this.selected = !this.selected;
                        return false;
                    }, false );
            });
        }

        multiSelectWithoutCtrl('#assigned_users')
        multiSelectWithoutCtrl('#assigned_user')
    </script>
</body>
</html>



