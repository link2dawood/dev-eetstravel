<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo trans('main.Itinerary'); ?> - <?php echo e($tour->name); ?></title>
    <!-- Bootstrap -->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <style>
        .tour-image {
            <?php if($tour->attachments()->first() == null): ?>
                height: 150px;
            <?php endif; ?>
        }

        .tms-logo {
            position: absolute;
            left: 15px;
            top: 5px;
            width: 75px;
            height: 70px;
        }

        .tms-logo-maxi {
            position: absolute;
            left: 50px;
            top: 10px;
            width: 125px;
            height: 110px;
        }

        .rectangle {
            background-color: #FFFFFF;
            box-shadow: 0 30px 40px 0 rgba(30, 30, 36, 0.20000000298023224);
            padding: 10px;

        }

        .header-grey {
            color: #6F6F7A;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            font-style: italic;
            font-weight: 400;
            line-height: 24px;
            text-align: left;
            padding: 10px 0px 0 50px;
        }

        .header-bl {
            color: #3D3C4A;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 24px;
            font-weight: 600;
            line-height: 30px;
            text-align: left;
            padding: 10px 10px 30px 50px;
        }

        .info-box {
            width: 100%;
            height: 100%;
            padding: 50px;
            margin-top: 10%;
        }

        .pic-box {
            width: 100%;
            margin-top: 10%;
            border-radius: 2px;
            padding: 10px;
            box-shadow: 0 30px 40px 0 rgba(30, 30, 36, 0.20000000298023224);
        }

        .info-header {
            color: #6F6F7A;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 24px;
            font-weight: 400;
            line-height: 30px;
            text-align: left;
        }

        .info-descr {
            color: #6F6F7A;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: 29px;
            width: 80%;
            text-align: justify;
        }

        .info-name {
            color: #3D3C4A;
            font-family: SignPainter;
            font-size: 45px;
            font-weight: 600;
            line-height: 56px;
            text-align: left;
        }

        .info-pending {
            color: #6F6F7A;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 24px;
            font-weight: 400;
            line-height: 30px;
            text-align: left;
        }

        .info-telfax {
            color: #6F6F7A;
            font-family: OpenSans;
            font-size: 14px;
            font-weight: 400;
            line-height: 24px;

            text-align: left;
        }

        .pic {
            width: 100%;
            height: 100%;
        }

        .day {
            margin: auto;
            padding-top: 40px;
        }

        /*
        */
    </style>

</head>
<?php
    $peopleCount = 0;
    $roomsCodes = '';
    $countDay = 0;
    $parity = 0;
?>
<?php $__currentLoopData = $listRoomsHotel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $peopleCount += isset(App\TourPackage::$roomsPeopleCount[$item->room_types->code]) ? App\TourPackage::$roomsPeopleCount[$item->room_types->code] * $item->count : 0;
        $roomsCodes == '' ? ($roomsCodes = $item->count . $item->room_types->code) : ($roomsCodes .= ' + ' . $item->count . $item->room_types->code);
    ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
					<div class="logo mb-5">
                       
                        <img class="tms-logo d-block d-md-none" src="<?php echo e(asset('/') . '/img/eets_logo.png'); ?>">
                        <img class="tms-logo-maxi  d-none d-md-block" src="<?php echo e(asset('/') . '/img/eets_logo.png'); ?>">
                    </div>
                   
                </div>
                <div class="rectangle mt-5">
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <p class="header-grey">Tour name</p>
                            <p class="header-bl"><?php echo e($tour->name); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="header-grey">Dep Date - Ret Date</p>
                            <p class="header-bl"><?php echo e(\Carbon\Carbon::parse($tour->departure_date)->format('d/M')); ?> -
                                <?php echo e(\Carbon\Carbon::parse($tour->retirement_date)->format('d/M')); ?></p>
                        </div>

                        
                    </div>
                </div>
				 <p class="header-grey">Image for landing page</p>
				 <div class="tour-image mt-5">
                        <?php if($tour->attachments()->first() != null && isset($tour->attachments()->first()->url)): ?>
                            <img src="<?php echo e($tour->attachments()->first()->url); ?>" alt="<?php echo e($tour->name); ?>"
                                style="width:100%;">
                        <?php endif; ?>
                      
                    </div>
            </div>

        </div>



        <?php $__currentLoopData = $tourDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tourDay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
            $countDay++;
            $packageDescription = '';
            ?>
            <div class="row">
                <div class="col-md-12">
                    <h3 class="text-center day">Day <?php echo e($countDay); ?> -
                        <?php echo e((new \Carbon\Carbon($tourDay->date))->formatLocalized('%B %d, %Y (%A)')); ?></h3>

                </div>
            </div>
            <?php $__currentLoopData = $tourDay->packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($package->description_package): ?>
                    <?php
                    $packageDescription = $package->description;
                    ?>
                <?php else: ?>
                    <?php
                    $parity++;
                    ?>
                    <div class="row">

                        <?php if($parity % 2 == 0): ?>
                            <?php
                                $srv = $package->service();
                                $image = [];
						if(!empty($srv->files)){
                                foreach ($srv->files as $file) {
                                    if ($file->attach_content_type == 'image/png' || $file->attach_content_type == 'image/jpeg') {
                                        $image[] = [
                                            'id' => $file->id,
                                            'file_name' => $file->attach_file_name,
                                        ];
                                    }
                                }
							}
                            ?>
                            <?php $__currentLoopData = $image; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6 d-none d-md-block">
                                    <div class="pic-box">
                                        <img class="pic"
                                            src="<?php echo e(asset('system/App/File/attaches/000/000/' .str_pad($img['id'], 3, '0', STR_PAD_LEFT) . '/original/' . $img['file_name'])); ?>">
                                    </div>

                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            
                        <?php endif; ?>


                        <div class="col-md-6">
                            <div class="info-box">
                                <?php if(!in_array($package->id, $exclude)): ?>
                                    <p class="info-name"><?php echo e($package->name); ?></p>
                                    <?php
                                    $srv = $package->service();
                                    ?>
                                    <p class="info-pending">
                                        <?php echo e(Carbon\Carbon::parse($package->time_from)->format('H:i')); ?>

                                        <?php if(!$package->description_package): ?>
                                            - <?php echo e(Carbon\Carbon::parse($package->time_to)->format('H:i')); ?>

                                            <?php if($package->type !== null): ?>
                                                <?php echo e(ucfirst($serviceTypes[$package->type])); ?>

                                            <?php endif; ?>
                                            <?php echo e($package->getStatusName()); ?>

                                        <?php endif; ?>
                                    </p>
                                    <?php if($srv): ?>
                                        <?php if($srv->work_phone): ?>
                                            <p class="info-telfax"> <?php echo trans('main.Tel'); ?>: <?php echo e($srv->work_phone); ?>

                                                <?php endif; ?> <?php if($srv->work_fax): ?>
                                                    Fax: <?php echo e($srv->work_fax); ?> </p>
                                        <?php endif; ?>
                                        
                                    <?php endif; ?>


                                    <?php if($packageDescription != ''): ?>
                                        <p class="info-descr"><?php echo $packageDescription; ?></p>
                                        <?php
                                        $packageDescription = '';
                                        ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if($parity % 2 != 0): ?>
                            <?php

                                $image = [];
								if(!empty($srv->files)){
                                foreach ($srv->files as $file) {
                                    if ($file->attach_content_type == 'image/png' || $file->attach_content_type == 'image/jpeg') {
                                        $image[] = [
                                            'id' => $file->id,
                                            'file_name' => $file->attach_file_name,
                                        ];
                                    }
                                }
													}

                            ?>
                            <?php $__currentLoopData = $image; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6 d-none d-md-block">
                                    <div class="pic-box">
                                        <img class="pic"
                                            src="<?php echo e(asset('system/App/File/attaches/000/000/' .str_pad($img['id'], 3, '0', STR_PAD_LEFT) . '/original/' . $img['file_name'])); ?>">
                                    </div>

                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            
                        <?php endif; ?>

                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>
</body>

</html>
<?php /**PATH /var/www/html/resources/views/export/landing_page.blade.php ENDPATH**/ ?>