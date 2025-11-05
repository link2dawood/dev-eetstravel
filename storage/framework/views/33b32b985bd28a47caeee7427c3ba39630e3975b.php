<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminTMS | Log in</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap.min.css')); ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?php echo e(asset('css/AdminLTE.min.css')); ?>">
    <!-- iCheck -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/square/blue.css">

    <style>
        .login-page {
            background: #d2d6de;
        }
        .login-box {
            margin: 7% auto;
            width: 360px;
        }
        .login-logo {
            font-size: 35px;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 300;
        }
        .login-logo a {
            color: #444;
        }
        .login-box-body {
            background: #fff;
            padding: 20px;
            border-radius: 0;
            color: #666;
        }
        .login-box-msg {
            margin: 0;
            text-align: center;
            padding: 0 20px 20px 20px;
        }
    </style>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="<?php echo e(url('/home')); ?>"><b>Admin</b>TMS</a>
        </div>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="login-box-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form action="<?php echo e(url('/login')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <div class="form-group has-feedback">
                    <input type="email" 
                           class="form-control" 
                           placeholder="Email" 
                           name="email" 
                           value="<?php echo e(old('email')); ?>" 
                           required 
                           autofocus>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback">
                    <input type="password" 
                           class="form-control" 
                           placeholder="Password" 
                           name="password" 
                           required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>

                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember"> Remember Me
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div>
                </div>
            </form>

            <a href="<?php echo e(url('/password/reset')); ?>">I forgot my password</a><br>
        </div>
    </div>

    <!-- jQuery 3 -->
    <script src="<?php echo e(asset('js/jquery.min.js')); ?>"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo e(asset('js/bootstrap.min.js')); ?>"></script>
    <!-- iCheck -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });
        });
    </script>
</body>
</html>

<?php /**PATH D:\xampp\htdocs\dev-eetstravel\resources\views/auth/simple-login.blade.php ENDPATH**/ ?>