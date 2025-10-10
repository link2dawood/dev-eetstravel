
<?php
use App\Status;
use App\Task;
use App\Tour;
use App\User;
$tours = tour::all();
$users = User::orderBy('name')->get();
$statuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'task')->get();

?>
<div id="modalCreate1" class="modal fade in" role="dialog" aria-labelledby="modalCreateLabel" style="padding-left: 17px;padding-right: 17px;"><label for="cars">Choose a car:</label>



    <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <a class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </a>
                <h4 id="modalCreateLabel" class="modal-title"><?php echo e(trans('main.Createtask')); ?></h4>
            </div>

            <?php if(count($errors) > 0): ?>
            <br>
            <div class="alert alert-danger">
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <br>
            <?php endif; ?>
            <form method='POST' action='<?php echo route("task.store"); ?>'>

                <div class="box box-body" style="border-top: none">
                    <div class="modal-body">
                        <input type='hidden' name='_token' value='<?php echo e(Session::token()); ?>'>
                        <input type='hidden' name='modal_create' value="1">
                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea name="content" id="content" class="form-control" style="resize: none"><?php echo e(old('content')); ?></textarea>
                        </div>

                        <div class="form-group col-md-6 col-lg-6" style="padding-left: 0;">

                            <label for="departure_date"><?php echo e(trans('main.Deadline')); ?></label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <?php echo Form::text('end_date', Carbon\Carbon::now()->format('Y-m-d'), ['class' => 'form-control pull-right datepicker', 'id' => 'start_date']); ?>

                            </div>
                        </div>

                        <div class="form-group col-md-6 col-lg-6" style="padding-right: 0">

                            <label for="departure_date"><?php echo e(trans('main.Time')); ?></label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </div>
                                <?php echo Form::text('end_time', '18:00', ['class' => 'form-control pull-right timepicker', 'id' => 'end_time']); ?>

                            </div>
                            <div class="tours"></div>
                        </div>

                        <div class="form-group" id = "tour_div" style="display:none">
                            <label for="tours"><?php echo e(trans('main.Tour')); ?></label>
                            <select name="tours" id="tours" class="form-control">

                                <?php $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tour->id); ?>"><?php echo e($tour->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="task_type"><?php echo trans('main.Tasktype'); ?></label>
                            <?php echo Form::select('task_type', \App\Task::$taskTypes, 0, ['class' => 'form-control task_type']); ?>

                        </div>
                        <div class="form-group">
                            <label for="status"><?php echo trans('main.Status'); ?></label>
                            <select name="status" id="status" class="form-control">
                                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option <?php echo e(old('status') == $status->id ? 'selected' : ''); ?> value="<?php echo e($status->id); ?>"><?php echo e($status->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div id="create_task_popup">
                            <div v-if="loading">
                            </div>

                            <div v-else>
                                

                            

                        <div class="form-group">
                            <label for="assigned_users"><?php echo trans('main.AssignedUser'); ?></label>
                            <select name="assigned_user[]" class="task-user js-state form-control select2" multiple="multiple" id="assigned_user[]">
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        

                    
        </div>
    </div>
    <div class="checkbox">
        <label>
            <?php echo Form::checkbox('priority'); ?>

            Priority
        </label>
    </div>
</div>
<div class="modal-footer">
    <a href="close" class='btn btn-warning' data-dismiss="modal"><?php echo e(trans('main.Close')); ?></a>
    <button class='btn btn-success pre-loader-func' type='submit'><?php echo e(trans('main.Save')); ?></button>
</div>
</div>
</form>
</div>
</div>
</div>
<?php $__env->startPush('scripts'); ?>









<?php $__env->stopPush(); ?>

<script>
    $(function() {
        Vue.component('select2', {
            template: '<select :id="id" :name="name" style="width: 100%"> <option v-if="customFirst" :value="customValue">{{customText}}</option><option>black</option</select>',
            props: {
                options: {
                    type: Array,
                    default: function() {
                        return []
                    }
                },
                id: {
                    default: ''
                },
                name: {
                    default: ''
                },
                customFirst: {
                    type: Boolean,
                    default: false
                },
                customValue: {
                    default: ''
                },
                customText: {
                    default: ''
                },
                allowClear: {
                    type: Boolean,
                    default: false
                },
                placeholder: {
                    type: Boolean,
                    default: ''
                },
                multiple: {
                    type: Boolean,
                    default: false
                },
                value: {
                    twoWay: true,
                    type: Array
                },
                change: {
                    type: Function
                },
            },
            mounted: function() {
                var self = this;
                var config = {
                    debug: false,
                    data: self.options,
                    placeholder: self.placeholder
                };
                if (self.multiple === true) {
                    $(self.$el).attr('multiple', true)
                    if (self.allowClear === true) {
                        config.allowClear = true
                    }
                }
                $(document).ready(function() {

                    if (!$(self.$el).hasClass('select2-hidden-accessible')) {

                        $(self.$el).select2(config).on('select2:select select2:unselect', function() {
                            for (item in $(self.$el).select2('data')) {
                                return d.id;
                            }
                            self.$set('value', v)
                            if (_.isFunction(self.change)) self.change(v)
                        })
                        $(self.$el).val(self.value).trigger('change')
                    }

                })
            }
        });
        new Vue({
            el: '#create_task_popup',
            data: {
                loading: true,
                users: [],
                toursAttachedUser: [],
                statuses: [],
                taskTypes: [],
                taskTypeValue: [1],
            },
            mounted: function() {
                var self = this;
                var userId = $('meta[name="user-id"]').attr('content');

                $.ajax({
                    url: '/api/v1/dashboard/create_task_popup',
                    method: 'GET',
                    data: {
                        'userId': userId
                    },
                    dataType: "json",
                    success: function(data) {

                        self.users = $(".tour").html(`<h1>${data.users}</h1>`);
                        self.toursAttachedUser = data.toursAttachedUser;
                        self.statuses = data.statuses;
                        self.taskTypes = data.taskTypes;
                        self.loading = false;
                    },
                    error: function(error) {
                        console.log(error);
                        self.loading = false;

                    }
                });
            },
            methods: {
                fetchData: function() {
                    var self = this;
                    var userId = $('meta[name="user-id"]').attr('content');

                    $.ajax({
                        url: '/api/v1/dashboard/create_task_popup',
                        method: 'GET',
                        data: {
                            'userId': userId
                        },
                        dataType: "json",
                        success: function(data) {

                            self.users = $(".tour").html(`<h1>${data.users}</h1>`);
                            self.toursAttachedUser = data.toursAttachedUser;
                            self.statuses = data.statuses;
                            self.taskTypes = data.taskTypes;
                            self.loading = false;
                        },
                        error: function(error) {
                            console.log(error);
                            self.loading = false;

                        }
                    });
                },

            }
        });

    });
	
	 $(".task_type").change(function() {
        if($(this).val() === "2"){
        
            $("#tour_div").css("display","block");
        }
        else{
            $("#tour_div").css("display","none");
        }
    })
</script><?php /**PATH /var/www/html/resources/views/scaffold-interface/dashboard/components/create_task_popup.blade.php ENDPATH**/ ?>