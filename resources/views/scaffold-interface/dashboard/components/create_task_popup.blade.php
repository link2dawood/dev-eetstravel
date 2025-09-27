{{--Popup create--}}
@php
use App\Status;
use App\Task;
use App\Tour;
use App\User;
$tours = tour::all();
$users = User::orderBy('name')->get();
$statuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'task')->get();

@endphp
<div id="modalCreate1" class="modal fade in" role="dialog" aria-labelledby="modalCreateLabel" style="padding-left: 17px;padding-right: 17px;"><label for="cars">Choose a car:</label>



    <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <a class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </a>
                <h4 id="modalCreateLabel" class="modal-title">{{ trans('main.Createtask') }}</h4>
            </div>

            @if (count($errors) > 0)
            <br>
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <br>
            @endif
            <form method='POST' action='{!!route("task.store")!!}'>

                <div class="box box-body" style="border-top: none">
                    <div class="modal-body">
                        <input type='hidden' name='_token' value='{{Session::token()}}'>
                        <input type='hidden' name='modal_create' value="1">
                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea name="content" id="content" class="form-control" style="resize: none">{{ old('content') }}</textarea>
                        </div>

                        <div class="form-group col-md-6 col-lg-6" style="padding-left: 0;">

                            <label for="departure_date">{{ trans('main.Deadline') }}</label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                {!! Form::text('end_date', Carbon\Carbon::now()->format('Y-m-d'), ['class' => 'form-control pull-right datepicker', 'id' => 'start_date']) !!}
                            </div>
                        </div>

                        <div class="form-group col-md-6 col-lg-6" style="padding-right: 0">

                            <label for="departure_date">{{ trans('main.Time') }}</label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </div>
                                {!! Form::text('end_time', '18:00', ['class' => 'form-control pull-right timepicker', 'id' => 'end_time']) !!}
                            </div>
                            <div class="tours"></div>
                        </div>

                        <div class="form-group" id = "tour_div" style="display:none">
                            <label for="tours">{{ trans('main.Tour') }}</label>
                            <select name="tours" id="tours" class="form-control">

                                @foreach($tours as $tour)
                                <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="task_type">{!!trans('main.Tasktype')!!}</label>
                            {!! Form::select('task_type', \App\Task::$taskTypes, 0, ['class' => 'form-control task_type']) !!}
                        </div>
                        <div class="form-group">
                            <label for="status">{!!trans('main.Status')!!}</label>
                            <select name="status" id="status" class="form-control">
                                @foreach($statuses as $status)
                                <option {{ old('status') == $status->id ? 'selected' : '' }} value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="create_task_popup">
                            <div v-if="loading">
                            </div>

                            <div v-else>
                                {{--<div class="form-group">
                                    <label for="tours">{{ trans('main.Tour') }}</label>
                                <select2 custom-first="true" custom-text="" custom-value="0" name="tour" id="tour" :options="toursAttachedUser">

                                </select2>

                                <select name="tours" id="tours" class="form-control">

                                    @foreach($tours as $tour)
                                    <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                                    @endforeach
                                </select>
                            </div>--}}

                            {{-- <div class="form-group">
                                    <label for="assign">{{ trans('main.Assignto') }}</label>
                            <select name="assigned_user" id="assigned_user">
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div class="form-group">
                            <label for="assigned_users">{!!trans('main.AssignedUser')!!}</label>
                            <select name="assigned_user[]" class="task-user js-state form-control select2" multiple="multiple" id="assigned_user[]">
                                @foreach($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        {{--
                            <div class="form-group">
                                <label for="assigned_users">{{ trans('main.AssignedUser') }}</label>
                        <select2 name="assigned_user[]" id="assigned_user[]" :options="users" :allow-clear="true" :multiple="true">
                        </select2>
                    </div>
                    --}}

                    {{--
                            <div class="form-group">
                                <label for="task_type">{{ trans('main.Tasktype') }}</label>
                    <select2 name="task_type" id="task_type" :value.sync="taskTypeValue" :options="taskTypes" :allow-clear="true">

                    </select2>
                </div>

                <div class="form-group">
                    <label for="status">{{ trans('main.Status') }}</label>
                    <select2 name="status" id="status" :options="statuses" :allow-clear="true">
                    </select2>
                </div>
                --}}
        </div>
    </div>
    <div class="checkbox">
        <label>
            {!! Form::checkbox('priority') !!}
            Priority
        </label>
    </div>
</div>
<div class="modal-footer">
    <a href="close" class='btn btn-warning' data-dismiss="modal">{{ trans('main.Close') }}</a>
    <button class='btn btn-success pre-loader-func' type='submit'>{{ trans('main.Save') }}</button>
</div>
</div>
</form>
</div>
</div>
</div>
@push('scripts')

{{--<script>--}}
{{--$(function () {--}}
{{--// $('.search_tour_task').select2('destroy');--}}
{{--$('.search_tour_task').select2({--}}
{{--dropdownParent: $("#modalCreate")--}}
{{--});--}}
{{--});--}}
{{--</script>--}}
@endpush
{{--end Popup create--}}
<script>
    $(function() {
        Vue.component('select2', {
            template: '<select :id="id" :name="name" style="width: 100%"> <option v-if="customFirst" :value="customValue">@{{customText}}</option><option>black</option</select>',
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
</script>