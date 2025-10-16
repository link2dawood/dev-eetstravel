<div class="row">
    <div class="<?php if(isset($tour)): ?> col-md-6 <?php else: ?> col-md-12 <?php endif; ?>">

        <div class="panel panel-info table_photos">
            <div class="panel-heading"><?php echo trans('main.Photos'); ?></div>
            <div class="panel-body image">
                <?php $__currentLoopData = $files['image']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="panel panel-default del-container" style="display: inline-block; height: 325px; width: 350px;">
                    <div class="panel-heading">
                        
                            
                            <button class="btn btn-danger btn-xs del-attach" data-attach-id="<?php echo e($image->id); ?>" data-attach-url="<?php echo e(route('file_delete', ['id' => $image->id])); ?>">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true" title="Delete"></span>
                            </button>
                            
                    </div>
                    <div class="panel-body">
                        <a href="<?php echo e('/public'.$image->attach->url()); ?>"><img src="<?php echo e('/public'.$image->attach->url()); ?>" style="height: 250px; max-width: 325px"></a>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php if(isset($tour)): ?>
    <div class="col-md-6">
        <div class="panel panel-info table_photos">
            <div class="panel-heading"><?php echo trans('main.imageforlanding'); ?></div>
            <div class="panel-body image">
                <div class="form-group">
                    <div class="thumbnail text-center">
                        <img class="pic" style="max-height: 350px;" src="<?php if($tour->attachments()->first() != null): ?> <?php echo e($tour->attachments()->first()->url); ?> <?php endif; ?>" alt="Image for landing page" style="width:100%">
                        <div class="caption">

                            <div class="upload-btn-wrapper">
                                <label for="check" class="btn btn-primary">Change</label>
                                <input id="check" name="fileToUpload[]" data-model="Tour" data-id="<?php echo e($tour->id); ?>"class="fileToUpload"type="file"  style="display:none"/>
                            </div>
                        </div>
                    </div>
                            
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>


            <div class="panel panel-default">
                <div class="panel-heading"><?php echo trans('main.Files'); ?></div>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo trans('main.Name'); ?></th>
                            <th><?php echo trans('main.Uploaded'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $files['attach']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attach): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class='del-container'>
                            <td>
                                <div class="link_attach_file">
                                    <a href="<?php echo e(url('public/'.$attach->attach->url())); ?>" target="_blank" class="link_file">
                                        <span class="glyphicon glyphicon-paperclip"></span>
                                        <span class="name_link_file"><?php echo e($attach->attach_file_name); ?></span>
                                    </a>
                                </div>
                                <div style="display: inline-block" class="pull-right">
                                    
                                        
                                        <button class="btn btn-danger btn-xs del-attach" data-attach-url="<?php echo e(route('file_delete', ['id' => $attach->id])); ?>">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true" title="Delete"></span>
                                        </button>
                                        
                                </div>
                            </td>
                            <td><span><?php echo e($attach->created_at); ?></span></td>
                        </tr>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <style>
                .link_attach_file {
                    display: inline-block;
                }

                .link_attach_file > a {
                    color: #333;
                    text-decoration: none;
                }

                .link_attach_file > a > span {
                    line-height: 0;
                    vertical-align: middle;
                }

                .name_link_file{
                    margin-left: 10px;
                }

                .link_attach_file > a:hover .name_link_file{
                    text-decoration: underline;
                }
            </style>

            <?php $__env->startPush('scripts'); ?>
            <script>
                $('.link_file').click(function (e) {
                    window.open($(this).attr('href'));
                });
                $('.image').magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    gallery: {enabled: true}
                });
               $('.del-attach').on('click', function (e) {
					e.preventDefault();
					let elem = $(this).context;
					let deleteUrl = $(elem).attr('data-attach-url');

					// Ask for confirmation before proceeding
					let confirmDelete = confirm("Are you sure you want to delete this attachment?");

					if (confirmDelete) {
						$.ajax({
							url: deleteUrl,
							method: 'POST',
							data: {
								"_token": "<?php echo e(csrf_token()); ?>"
							},
							success: (res) => {
								$(this).closest('.del-container').hide();
							},
							error: (res) => {
								console.log(res);
							}
						});
					} else {
						// Do nothing if user cancels
						console.log("Deletion cancelled.");
					}
				});

            </script>
            <?php $__env->stopPush(); ?><?php /**PATH /var/www/html/resources/views/component/files.blade.php ENDPATH**/ ?>