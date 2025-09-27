<?php $__env->startSection('title', 'Create'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title', [
        'title' => 'Offer Emails',
        'sub_title' => 'Emails',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Invoices', 'icon' => 'suitcase', 'route' => route('tour.index')],
            ['title' => 'Create', 'route' => null],
        ],
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
				 
      
				<h1>Emails From Supplier</h1>
                        <div class="row">
                            <div class="col-md-12">
								 <?php if(!empty($emails)): ?>
                                <ul class="timeline">
                                   
                                        <?php $__currentLoopData = $emails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $email): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php 
                                            $dateString = $email->header->date??"";

                                            // Create a DateTime object from the string
                                            $dateTime = new DateTime($dateString);

                                            // Get the date and time separately
                                            $date = $dateTime->format('D m.Y'); // Format the date as desired
                                            $time = $dateTime->format('H:i:s');
											$mail = $email->header->details->sender[0]->mailbox."@".$email->header->details->sender[0]->host;
                                        ?>
                                        
                                            <li class="time-label">
                                                <span class="bg-red">
                                                    <?php echo e($date); ?> 
                                                </span>
                                            </li>


                                            <li>
                                                <i class="fa fa-envelope bg-blue"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="fa fa-clock-o"></i> <?php echo e($time); ?></span>
                                                    <h3 class="timeline-header"><a href="#"><?php echo e($email->header->from??""); ?></a> reply to your
														email<b> :<?php echo e($email->header->subject??""); ?></b></h3>
                                                    <div class="timeline-body">
                                                        <?php echo $email->message->html??""; ?>

                                                    </div>
                                                    <div class="timeline-footer">
                                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#exampleModal" onclick="myfunction('<?php echo e($mail); ?>','<?php echo e($email->header->subject); ?>')">reply to supplier</button>
                                                    </div>
                                                </div>
                                            </li>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                      
                                </ul>
								<?php else: ?>
										<p> Required Service does not reply yet Contact them for furthur Inquiry</p>
										<p>Or You do not include work email in tms dashboard yet</p>
									<?php endif; ?>
                            </div>

                        </div>
						<h1>Emails From TMS</h1>
                        <div class="row">
                            <div class="col-md-12">
								<?php if(!empty($tms_emails)): ?>
                                <ul class="timeline">
                                    
                                        <?php $__currentLoopData = $tms_emails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tmsemail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php 
                                            $dateString = $tmsemail->header->date??"";

                                            // Create a DateTime object from the string
                                            $dateTime = new DateTime($dateString);

                                            // Get the date and time separately
                                            $date = $dateTime->format('D m.Y'); // Format the date as desired
                                            $time = $dateTime->format('H:i:s');
                                        ?>
                                        
                                            <li class="time-label">
                                                <span class="bg-red">
                                                    <?php echo e($date); ?> 
                                                </span>
                                            </li>


                                            <li>
                                                <i class="fa fa-envelope bg-blue"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="fa fa-clock-o"></i> <?php echo e($time); ?></span>
                                                    <h3 class="timeline-header"><a href="#"><?php echo e($tmsemail->header->from??""); ?></a> sent 
														email to  Supplier<b> :<?php echo e($tmsemail->header->subject??""); ?></b></h3>
                                                    <div class="timeline-body">
                                                        <?php echo $tmsemail->message->html??""; ?>

                                                    </div>
                                               
                                            </li>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                      
                                </ul>
									<?php else: ?>
										<p> Required Service does not reply yet Contact them for furthur Inquiry</p>
										<p>Or You do not include work email in tms dashboard yet</p>
									<?php endif; ?>
                            </div>

                        </div>
            </div>
        </div>
	<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
						<form id="replyForm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
							
                            <div class="modal-body">
                                
                                    
                                    <div class="mb-3">
										<input type="hidden" name="email_sent" value="" id="email_sent">
										<input type="hidden" name="email_subject" value="" id="email_subject">
										<input type="hidden" name="package_id" value="<?php echo e($tour_package->id); ?>" id="package_id">
                                        <label for="message-text" class="col-form-label">Message:</label>
                                        <textarea name="body" id="div_editor1" cols="30" rows="10"></textarea>
                                    </div>
                               
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Send message</button>
                            </div>
                        </div>
							 </form>
                    </div>
                </div>

    </section>
    <script>
		function myfunction(mail,subject){
			$('#email_sent').val(mail);
			$('#email_subject').val(subject);
		}
		
		$('#replyForm').submit(function(e) {

                e.preventDefault();
                var form_data = new FormData(this);
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
                $.ajax({
                    type: "POST",
                    url: '/templates/<?php echo e($user->id); ?>/emails/reply',
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log("SUCCESS");
						location.reload();
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
                // alert("work")
            })
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('scaffold-interface.layouts.modernapp', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/tour_package/offers/offer_emails.blade.php ENDPATH**/ ?>