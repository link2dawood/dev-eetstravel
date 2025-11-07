<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php if(auth()->guard()->check()): ?>
        <meta name="user-id" content="<?php echo e(Auth::user()->id); ?>">
    <?php endif; ?>

    <title><?php echo $__env->yieldContent('title', 'TMS - Tour Management System'); ?></title>

    <!-- CSS files -->
    <link href="<?php echo e(asset('tabler/css/tabler.min.css')); ?>" rel="stylesheet"/>
    <link href="<?php echo e(asset('tabler/css/tabler-icons.min.css')); ?>" rel="stylesheet"/>
    <link rel="stylesheet" href="<?php echo e(asset('css/jquery.toast.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/fileinput.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/magnific.css')); ?>">
    <link href="<?php echo e(asset('css/select2.min.css')); ?>" rel="stylesheet"/>
    <link href="<?php echo e(asset('css/bootstrap-datetimepicker.min.css')); ?>" rel="stylesheet"/>
    <link href="<?php echo e(asset('css/bootstrap-datepicker.min.css')); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(asset('css/responsive-global.css')); ?>" rel="stylesheet" type="text/css"/>
    
    <!-- Modern UI Enhancements -->
    <link href="<?php echo e(asset('css/modern-forms.css')); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(asset('css/modern-tables.css')); ?>" rel="stylesheet" type="text/css"/>

    <style>
        @import  url('https://rsms.me/inter/inter.css');
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
            font-family: 'Font Awesome 5 Free', 'Font Awesome 5 Brands', 'tabler-icons' !important;
        }

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

        /* FontAwesome to Tabler Icon Mapping */
        .fa-dashboard::before { content: "\ea47"; font-family: 'tabler-icons'; } /* ti-home */
        .fa-home::before { content: "\ea47"; font-family: 'tabler-icons'; } /* ti-home */
        .fa-users::before { content: "\f508"; font-family: 'tabler-icons'; } /* ti-users */
        .fa-user::before { content: "\ebc9"; font-family: 'tabler-icons'; } /* ti-user */
        .fa-cog::before { content: "\ebd5"; font-family: 'tabler-icons'; } /* ti-settings */
        .fa-bell::before { content: "\ea35"; font-family: 'tabler-icons'; } /* ti-bell */
        .fa-envelope::before { content: "\eb03"; font-family: 'tabler-icons'; } /* ti-mail */
        .fa-tasks::before { content: "\ec1c"; font-family: 'tabler-icons'; } /* ti-checkbox */
        .fa-calendar::before { content: "\ea53"; font-family: 'tabler-icons'; } /* ti-calendar */
        .fa-briefcase::before { content: "\ea46"; font-family: 'tabler-icons'; } /* ti-briefcase */
        .fa-map-pin::before { content: "\ebe9"; font-family: 'tabler-icons'; } /* ti-map-pin */
        .fa-building::before { content: "\ea4c"; font-family: 'tabler-icons'; } /* ti-building */
        .fa-key::before { content: "\eaf4"; font-family: 'tabler-icons'; } /* ti-key */
        .fa-search::before { content: "\eb1c"; font-family: 'tabler-icons'; } /* ti-search */
        .fa-plus::before { content: "\eb0b"; font-family: 'tabler-icons'; } /* ti-plus */
        .fa-minus::before { content: "\eaf2"; font-family: 'tabler-icons'; } /* ti-minus */
        .fa-edit::before { content: "\eaea"; font-family: 'tabler-icons'; } /* ti-edit */
        .fa-trash::before { content: "\eb41"; font-family: 'tabler-icons'; } /* ti-trash */
        .fa-check::before { content: "\ea5e"; font-family: 'tabler-icons'; } /* ti-check */
        .fa-times::before { content: "\eb55"; font-family: 'tabler-icons'; } /* ti-x */
    </style>

    <?php echo $__env->yieldContent('colorpicker-css'); ?>
    <?php echo $__env->yieldContent('post_styles'); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>

    <script type="text/javascript" src="<?php echo e(asset('js/lib/jquery.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/moment.js')); ?>"></script>
    <script src="https://jsuites.net/v4/jsuites.js"></script>
    <link rel="stylesheet" href="https://jsuites.net/v4/jsuites.css" type="text/css" />
    <script type="text/javascript" src="<?php echo e(asset('js/jquery.toast.js')); ?>"></script>
    
    <!-- Axios for HTTP requests -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script src="<?php echo e(asset('js/vue.js')); ?>"></script>
    <script src="<?php echo e(asset('js/piexif.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/purify.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/fileinput.min.js')); ?>"></script>
</head>
<body>
    <audio src="/new_message.mp3" id="chat_message"></audio>

    <div class="loadingoverlay">
        <div class="spinner-border text-primary loadingoverlay_fontawesome" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="page">
        <?php if(auth()->guard()->check()): ?>
            <?php echo $__env->make('scaffold-interface.layouts.tabler-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>

        <div class="page-wrapper">
            <?php if(auth()->guard()->check()): ?>
                <?php echo $__env->make('scaffold-interface.layouts.tabler-header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>

            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    <?php echo $__env->make('component.session-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </div>

            <?php echo $__env->make('scaffold-interface.layouts.tabler-footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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

    <!-- Tabler Core -->
    <script src="<?php echo e(asset('tabler/js/tabler.min.js')); ?>"></script>

    <!-- Core JS -->
    <script type="text/javascript" src="<?php echo e(asset('js/lib/moment-with-locales.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(URL::asset('js/select2.min.js')); ?>"></script>
    <script> var baseURL = "<?php echo e(URL::to('/')); ?>"</script>
    <script type="text/javascript" src="<?php echo e(URL::asset('js/AjaxisBootstrap.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(URL::asset('js/scaffold-interface-js/customA.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/bootstrap-datetimepicker.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-datepicker.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/script.js')); ?>"></script>
    <script src="<?php echo e(asset('js/magnific.js')); ?>"></script>
    
    <!-- Google Maps API (load before google_places.js) -->
    <?php if(config('google.places.key')): ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(config('google.places.key')); ?>&libraries=places"></script>
    <?php endif; ?>
    
    <script src="<?php echo e(URL::asset('js/google_places.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('js/jquery.scrollTo.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/pusher.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/jquery.repeater.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/helper.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/onclick-events.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/notifications.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/ckeditor/ckeditor.js')); ?>"></script>
    <script src="<?php echo e(asset('js/ckeditor.js')); ?>"></script>
    <script src="<?php echo e(asset('js/icheck.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/cities.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/action-buttons.js')); ?>"></script>

    <?php echo $__env->yieldContent('colorpicker-js'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <?php echo $__env->yieldContent('post_scripts'); ?>
    <?php echo $__env->yieldContent('tour_package_script'); ?>
    <?php echo $__env->yieldContent('post_scripts_calendar'); ?>

    <script>
        <?php if(auth()->guard()->check()): ?>
            var user_email = "<?php echo e(Auth::user()->email_login); ?>";
        <?php else: ?>
            var user_email = "";
        <?php endif; ?>

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
<?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/scaffold-interface/layouts/tabler-app.blade.php ENDPATH**/ ?>