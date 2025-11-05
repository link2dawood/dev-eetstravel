@extends('scaffold-interface.layouts.tabler-app')
@section('title','Create Task')
@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="ti ti-home"></i> Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('task.index') }}">Tasks</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create</li>
                            </ol>
                        </nav>
                    </div>
                    <h2 class="page-title">Create New Task</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="javascript:history.back()" class="btn btn-primary">
                            <i class="ti ti-arrow-left"></i> {!!trans('main.Back')!!}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <i class="ti ti-alert-circle icon alert-icon"></i>
                                </div>
                                <div>
                                    <h4 class="alert-title">Validation Error</h4>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('task.store') }}" id="task-form">
                        @csrf
                        <input type='hidden' name='modal_create' value="0">

                        <div class="row">
                            <div class="col-md-8">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="ti ti-checklist me-2"></i> Task Details</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group mb-3">
                                                    <label class="form-label required">{{ trans('main.Content') }}</label>
                                                    <textarea 
                                                        name="content" 
                                                        class="form-control" 
                                                        rows="5" 
                                                        required
                                                        placeholder="Describe the task in detail"
                                                    >{{ old('content') }}</textarea>
                                                    <small class="form-hint">Describe the task in detail</small>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">{{ trans('main.Tour') }}</label>
                                                    <select name="tour" class="form-select" placeholder="{{ trans('main.WithoutTour') }}">
                                                        <option value="">{{ trans('main.WithoutTour') }}</option>
                                                        @foreach($tours as $tour)
                                                            <option {{ ($tour_default && $tour_default == $tour->id) ? 'selected' : '' }} value="{{ $tour->id }}">
                                                                {{ $tour->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="ti ti-calendar me-2"></i> Schedule</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="end_date" class="form-label">{!!trans('main.Deadline')!!}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ti ti-calendar"></i>
                                                        </span>
                                                        <input 
                                                            type="text" 
                                                            name="end_date" 
                                                            id="end_date" 
                                                            class="form-control datepicker" 
                                                            value="{{ Carbon\Carbon::now()->format('Y-m-d') }}"
                                                            autocomplete="off"
                                                        >
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="end_time" class="form-label">{!!trans('main.Time')!!}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ti ti-clock"></i>
                                                        </span>
                                                        <input 
                                                            type="text" 
                                                            name="end_time" 
                                                            id="end_time" 
                                                            class="form-control timepicker" 
                                                            value="18:00"
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="ti ti-users me-2"></i> Assignment</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label required">{!! trans('main.AssignedUser') !!}</label>
                                                <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column gap-2">
                                                    @foreach ($users as $user)
                                                    <label class="form-selectgroup-item flex-fill">
                                                        <input 
                                                            type="checkbox" 
                                                            name="assigned_user" 
                                                            id="user_{{ $user->id }}" 
                                                            value="{{ $user->id }}"
                                                            class="form-selectgroup-input user_checkboxes"
                                                        >
                                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                            <div class="me-3">
                                                                <span class="form-selectgroup-check"></span>
                                                            </div>
                                                            <div class="form-selectgroup-label-content d-flex align-items-center">
                                                                @if($user->gravatar)
                                                                <span class="avatar me-2" style="background-image: url({{ $user->gravatar }})"></span>
                                                                @else
                                                                <span class="avatar me-2">{{ substr($user->name, 0, 2) }}</span>
                                                                @endif
                                                                <div>
                                                                    <div>{{ $user->name }}</div>
                                                                    <div class="text-muted small">{{ $user->email }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="ti ti-flag me-2"></i> Status, Priority & Type</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label required">{{ trans('main.Status') }}</label>
                                                @php
                                                    $statusOptions = $statuses->pluck('name', 'id')->toArray();
                                                @endphp
                                                <select name="status" class="form-select" required>
                                                    <option value="" disabled {{ old('status') ? '' : 'selected' }}>Select a status</option>
                                                    @foreach($statusOptions as $id => $name)
                                                        <option value="{{ $id }}" {{ old('status') == $id ? 'selected' : '' }}>
                                                            {{ $name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label required">Task Type</label>
                                                @php
                                                    // Get task types from the Task model
                                                    $taskTypes = \App\Task::$taskTypes;
                                                @endphp
                                                <select name="task_type" class="form-select" required>
                                                    <option value="" disabled {{ old('task_type') ? '' : 'selected' }}>Select a type</option>
                                                    @foreach($taskTypes as $id => $name)
                                                        <option value="{{ $id }}" {{ old('task_type') == $id ? 'selected' : '' }}>
                                                            {{ $name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Priority</label>
                                                <div class="form-selectgroup">
                                                    <label class="form-selectgroup-item">
                                                        <input type="radio" name="priority" value="low" class="form-selectgroup-input" checked>
                                                        <span class="form-selectgroup-label">
                                                            <i class="ti ti-flag text-info"></i> Low
                                                        </span>
                                                    </label>
                                                    <label class="form-selectgroup-item">
                                                        <input type="radio" name="priority" value="medium" class="form-selectgroup-input">
                                                        <span class="form-selectgroup-label">
                                                            <i class="ti ti-flag text-warning"></i> Medium
                                                        </span>
                                                    </label>
                                                    <label class="form-selectgroup-item">
                                                        <input type="radio" name="priority" value="high" class="form-selectgroup-input">
                                                        <span class="form-selectgroup-label">
                                                            <i class="ti ti-flag text-danger"></i> High
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-2"></i>
                                            {!! trans('main.Save') !!}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date picker
    // Make sure you have the datepicker library loaded in your tabler-app.blade.php
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    // Initialize time picker
    // Make sure you have the timepicker library loaded in your tabler-app.blade.php
    $('.timepicker').timepicker({
        showMeridian: false,
        defaultTime: '18:00'
    });

    // Validate at least one user is selected
    const form = document.getElementById('task-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedUsers = document.querySelectorAll('.user_checkboxes:checked');
            if (checkedUsers.length === 0) {
                e.preventDefault();
                alert('Please assign at least one user to this task.');
                return false;
            }
        });
    }
});
</script>
@endpush