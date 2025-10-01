<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $__env->yieldContent('title'); ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php if(auth()->guard()->check()): ?>
        <meta name="user-id" content="<?php echo e(Auth::user()->id); ?>">
    <?php endif; ?>

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/fileinput.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/font-awesome-4.7.0/css/font-awesome.min.css')); ?>">

    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo e(asset('css/ionicons.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/magnific.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/jquery.toast.css')); ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo e(asset('css/adminlte-app.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-tables.css')); ?>">

    <?php echo $__env->yieldContent('colorpicker-css'); ?>

    <link rel="stylesheet" href="<?php echo e(asset('css/skins/_all-skins.min.css')); ?>">
    <link rel="stylesheet" href="/css/util.css">
    <link rel="stylesheet" href="/plugins/iCheck/all.css">
    <link rel="stylesheet" href="<?php echo e(asset('css/plugins/export.css')); ?>" type="text/css" media="all"/>

    <script type="text/javascript" src="<?php echo e(asset('js/lib/jquery.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/moment.js')); ?>"></script>
    <script src="https://jsuites.net/v4/jsuites.js"></script>
    <link rel="stylesheet" href="https://jsuites.net/v4/jsuites.css" type="text/css" />
    <script type="text/javascript" src="<?php echo e(asset('js/jquery.toast.js')); ?>"></script>
    <script src="<?php echo e(asset('js/vue.js')); ?>"></script>
    <script src="<?php echo e(asset('js/piexif.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/purify.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/fileinput.min.js')); ?>"></script>

    <!--[if lt IE 9]>
    <script src="<?php echo e(asset('js/html5shiv.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/respond.min.js')); ?>"></script>
    <![endif]-->

</head>
<body class="hold-transition skin-blue sidebar-mini ">

<audio src="/new_message.mp3" id="chat_message"></audio>

<div class="loadingoverlay">
    <div class="loadingoverlay_fontawesome fa fa-spinner fa-spin"></div>
</div>

<div class="wrapper" >
    <header class="main-header">
        <a href="<?php echo e(url('home')); ?>" class="logo">
            <span class="logo-mini"><b>TMS</b></span>
            <span class="logo-lg"><b>TMS</b></span>
        </a>
        <nav class="navbar navbar-static-top">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only"><?php echo e(trans('main.Togglenavigation')); ?></span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown notifications-menu notifications-content"></li>
                    <?php if(auth()->guard()->check()): ?>
                        <?php
                            $messages = \App\Helper\DashboardHelper::getCountUnreadMailMessage();
                            $tasks = \App\Helper\DashboardHelper::getTasks();
                        ?>

                        <li class="dropdown messages-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-envelope-o"></i>
                                <?php if($messages): ?>
                                    <span class="label label-danger"><?php echo e($messages); ?></span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <ul class="list_notification_email">
                                    <li class="footer">
                                        <a href="<?php echo e(route('email.index')); ?>"><?php echo e(trans('main.Viewall')); ?></a>
                                    </li>
                                    <li class="footer">
                                        <a class="<?php echo e(!$messages ? 'disabled-link' : ''); ?>"
                                           href="<?php echo e(route('email.readAll')); ?>"><?php echo e(trans('main.Readall')); ?></a>
                                    </li>
                                </ul>
                            </ul>
                        </li>
                        <li class="dropdown tasks-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-flag-o"></i>
                                <span class="label label-danger"><?php echo e((@$tasks) ? @$tasks->count : ""); ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header"><?php echo e(trans('main.Youhave')); ?> <?php echo e((@$tasks) ? @$tasks->count : ""); ?> <?php echo e(trans('main.tasks')); ?></li>
                                <li>
                                    <ul class="menu menuTasks">
                                        <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <a href="<?php echo route('task.show', ['task' => $task->id]); ?>"
                                                   class="my_notif_tasks">
                                                    <div class="image_tasks_notif">
                                                        <i class="fa fa-users text-aqua"></i>
                                                    </div>
                                                    <div class="desc_tasks_notif">
                                                        <span><?php echo $task->tourNameNotification(); ?></span>
                                                        <small><?php echo $task->dead_line; ?></small>
                                                    </div>
                                                </a>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </li>
                                <li class="footer">
                                    <a href="/profile/?tab=history-tasks-tab"><?php echo e(trans('main.Viewtasks')); ?></a>
                                </li>
                            </ul>
                        </li>
                        <li class="user user-menu">
                            <a href="<?php echo e(url('profile')); ?>">
                                <img src="<?php echo e(Auth::user()->avatar ? asset(Auth::user()->avatar) : asset('img/avatar.png')); ?>" class="user-image" alt="User Image" style="text-align: center;">
                                <span class="hidden-xs"><?php echo e(Auth::user()->name); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <?php if(auth()->guard()->check()): ?>
        <aside class="main-sidebar fix-sidebar">
            <?php echo $__env->make('scaffold-interface.layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </aside>
    <?php endif; ?>

    <div class="content-wrapper" >
        <?php echo $__env->make('component.session-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->yieldContent('content'); ?>
    </div>
    <script type="text/javascript" src="<?php echo e(asset('js/appi.js')); ?>"></script>
</div>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class='AjaxisModal'>
    </div>
</div>

<div class="protect_loader hidden">
    <div class="loadingoverlay_fontawesome fa fa-spinner fa-spin" ></div>
</div>

<script type="text/javascript" src="<?php echo e(asset('js/app.min.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/lib/moment-with-locales.js')); ?>"></script>
<link href="<?php echo e(asset('css/select2.min.css')); ?>" rel="stylesheet"/>
<link href="<?php echo e(asset('css/bootstrap-datetimepicker.min.css')); ?>" rel="stylesheet"/>

<script type="text/javascript" src="<?php echo e(URL::asset('js/select2.min.js')); ?>"></script>
<script> var baseURL = "<?php echo e(URL::to('/')); ?>"</script>
<script type="text/javascript" src="<?php echo e(URL::asset('js/AjaxisBootstrap.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(URL::asset('js/scaffold-interface-js/customA.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/bootstrap-datetimepicker.min.js')); ?>"></script>
<?php echo $__env->yieldContent('colorpicker-js'); ?>

<link href="<?php echo e(asset('css/bootstrap-datepicker.min.css')); ?>" rel="stylesheet" type="text/css"/>

<script src="<?php echo e(asset('js/bootstrap-datepicker.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/script.js')); ?>"></script>
<script src="<?php echo e(asset('js/magnific.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/lib/bootstrap.min.js')); ?>"></script>

<script src="<?php echo e(URL::asset('js/google_places.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/jquery.scrollTo.min.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/pusher.min.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/jquery.repeater.min.js')); ?>"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
<?php echo $__env->yieldContent('post_styles'); ?>
<?php echo $__env->yieldContent('tour_package_script'); ?>
<?php echo $__env->yieldContent('post_scripts'); ?>
<?php echo $__env->yieldContent('post_scripts_calendar'); ?>

<script type="text/javascript" src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/helper.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/onclick-events.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/notifications.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/ckeditor/ckeditor.js')); ?>"></script>

<script src="<?php echo e(asset('js/ckeditor.js')); ?>"></script>
<script src="<?php echo e(asset('js/icheck.min.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/cities.js')); ?>"></script>

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
</html><?php /**PATH /var/www/html/resources/views/scaffold-interface/layouts/app.blade.php ENDPATH**/ ?>