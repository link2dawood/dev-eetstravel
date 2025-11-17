@extends('scaffold-interface.layouts.tabler-app')
@section('title','Show')
@section('content')
@include('layouts.title', [
    'title' => 'Profile',
    'sub_title' => $user->name,
    'breadcrumbs' => [
        ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
        ['title' => 'Profile', 'route' => null]
    ]
])

<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ trans('main.Userprofile') }}</div>
                <h2 class="page-title"><i class="ti ti-user-circle me-2"></i>{{ $user->name }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ url('profile/edit') }}" class="btn btn-primary">
                    <i class="ti ti-edit me-1"></i>{{ trans('main.Edit') }}
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" href="#info_profile" role="tab" data-bs-toggle="tab">
                        <i class="ti ti-address-card me-1"></i>{{ trans('main.Info') }}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="#timeline" role="tab" data-bs-toggle="tab">
                        <i class="ti ti-history me-1"></i>{{ trans('main.History') }}
                    </a>
                </li>
                @if(Auth::user()->can('task.index'))
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" href="#history_tasks_tab" role="tab" data-bs-toggle="tab" id="history-tasks-tab">
                            <i class="ti ti-checklist me-1"></i>{{ trans('main.Tasks') }}
                        </a>
                    </li>
                @endif
                @if(Auth::user()->can('tour.index'))
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" href="#history_tours_tab" role="tab" data-bs-toggle="tab" id="history-tours-tab">
                            <i class="ti ti-plane me-1"></i>{{ trans('main.Tours') }}
                        </a>
                    </li>
                @endif
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="#notifications" role="tab" data-bs-toggle="tab" id="notifications-tab">
                        <i class="ti ti-bell me-1"></i>{{ trans('main.Notifications') }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                        <div class="tab-pane fade show active" id="info_profile">
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <img class="rounded-circle mb-3" width="120" height="120"
                                                 src="{{ $user->avatar ? asset($user->avatar) : asset('img/avatar.png') }}"
                                                 alt="{{ $user->name }}">
                                            <h3 class="mb-1">{{ $user->name }}</h3>
                                            <p class="text-muted mb-3">{{ $user->email }}</p>
                                            <div class="text-start">
                                                <strong class="d-block text-muted small mb-1"><i class="ti ti-school me-1"></i>{{trans('main.Education')}}</strong>
                                                <p class="mb-2">{{ $user->education ?? '—' }}</p>
                                                <strong class="d-block text-muted small mb-1"><i class="ti ti-map-pin me-1"></i>{{trans('main.Location')}}</strong>
                                                <p class="mb-2">{{ $user->location ?? '—' }}</p>
                                                <strong class="d-block text-muted small mb-1"><i class="ti ti-file-description me-1"></i>{{trans('main.Notes')}}</strong>
                                                <p class="mb-0">{{ $user->note ?? '—' }}</p>
                                            </div>
                                            <hr>
                                            <strong class="d-block text-muted mb-2">{{trans('main.Updateemails')}}</strong>
                                            <div class="row g-2 align-items-center">
                                                <div class="col-4">
                                                    <input type="number" min="1" class="form-control" id="time_period" placeholder="5">
                                                </div>
                                                <div class="col-4">
                                                    <select name="update" id="period_type" class="form-select">
                                                        <option value="D">{{trans('main.Days')}}</option>
                                                        <option value="H">{{trans('main.Hours')}}</option>
                                                    </select>
                                                </div>
                                                <div class="col-4">
                                                    <button type="button" class="btn btn-success w-100" id="period_submit">{{trans('main.Go')}}</button>
                                                </div>
                                            </div>
                                            <div class="alert alert-info d-none mt-3" id="alert-message">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-info-circle me-2"></i>
                                                    <span>{{trans('main.Theprocessisrunning')}}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-center">
                                            <button class="btn btn-outline-secondary w-100" id="logout" onclick="event.preventDefault(); logoutForm();">
                                                <i class="ti ti-logout me-1"></i>{{trans('main.Signout')}}
                                            </button>
                                            <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="d-none">
                                                {{ csrf_field() }}
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-8">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            @if (count($errors) > 0)
                                                <div class="alert alert-danger">
                                                    <ul class="mb-0">
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            @if (session('incorrect_data'))
                                                <div class="alert alert-danger text-center">
                                                    {{ session('incorrect_data') }}
                                                </div>
                                            @endif

                                            <form class="row g-3" action="{{url('/users/'.$user->id)}}" method="post"
                                                  enctype="multipart/form-data">
                                                {!! csrf_field() !!}

                                                <input type="hidden" name="user_id" value="{{$user->id}}">

                                                <div class="col-md-6">
                                                    <label for="email" class="form-label">{{trans('main.Email')}}</label>
                                                    <input type="email" name="email"
                                                           value="{{ old('email', $user->email) }}"
                                                           class="form-control" id="email" placeholder="Email" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">{{trans('main.Name')}}</label>
                                                    <input type="text" name="name"
                                                           value="{{ old('name', $user->name) }}"
                                                           class="form-control" id="name" placeholder="Name" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="password" class="form-label">{{trans('main.Password')}}</label>
                                                    <input type="password" name="password" class="form-control"
                                                           placeholder="••••••">
                                                    <small class="text-muted">{{ __('Leave blank to keep current password') }}</small>
                                                </div>

                                                <div class="col-md-6"></div>

                                                <div class="col-md-6">
                                                    <label for="email_login" class="form-label">{{trans('main.EmailLogin')}}</label>
                                                    <input type="email"
                                                           name="email_login"
                                                           value="{{ old('email_login', $user->email_login) }}"
                                                           class="form-control"
                                                           id="email_login"
                                                           placeholder="user@example.com">
                                                    <small class="text-muted">
                                                        {{ __('Used for SnappyMail connection') }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="email_password" class="form-label">{{trans('main.Email')}} {{trans('main.Password')}}</label>
                                                    <input type="password"
                                                           name="email_password"
                                                           class="form-control"
                                                           id="email_password"
                                                           placeholder="{{ __('Enter new SnappyMail password') }}">
                                                    <small class="text-muted">
                                                        {{ __('Leave blank to keep the existing password.') }}
                                                    </small>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="education" class="form-label">{{trans('main.Education')}}</label>
                                                    <input type="text" name="education"
                                                           value="{{ old('education', $user->education) }}"
                                                           class="form-control" placeholder="Education">
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="location" class="form-label">{{trans('main.Location')}}</label>
                                                    <input type="text" name="location"
                                                           value="{{ old('location', $user->location) }}"
                                                           class="form-control" placeholder="Location">
                                                </div>

                                                <div class="col-12">
                                                    <label for="note" class="form-label">{{trans('main.Note')}}</label>
                                                    <input type="text" name="note"
                                                           value="{{ old('note', $user->note) }}"
                                                           class="form-control" placeholder="Note">
                                                </div>

                                                <div class="col-12">
                                                    <label for="avatar" class="form-label">{{trans('main.Image')}}</label>
                                                    <input id="avatar" name="avatar" type="file" class="form-control">
                                                </div>

                                                <input type="hidden" name="edit_profile" value="1">

                                                <div class="col-12 text-end">
                                                    <button type="submit" class="btn btn-primary">{{trans('main.Submit')}}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div role='tabpanel' class="tab-pane fade" id="history_tasks_tab">
                            @include('component.list_tasks_for_profile', ['userName' => $user->name, 'userId' => $user->id, 'tasks' => $tasks])
                        </div>

                        <div role='tabpanel' class="tab-pane fade" id="history_tours_tab">
                            @include('component.list_tours_for_profile', ['userName' => $user->name, 'userId' => $user->id, 'tours' => $tours])
                        </div>

                        <div class="tab-pane fade" id="timeline">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="timeline timeline-simple">
                                        @forelse($activities as $activity)
                                            <div class="timeline-item">
                                                <div class="timeline-item-marker">
                                                    <div class="timeline-item-marker-text">{{$activity->updated_at->format('d-m-Y')}}</div>
                                                    <div class="timeline-item-marker-indicator bg-primary"></div>
                                                </div>
                                                <div class="timeline-item-content">
                                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                        <h3 class="h5 mb-0">{{$activity->log_name}}</h3>
                                                        <small class="text-muted">
                                                            <i class="ti ti-clock me-1"></i>{{$activity->updated_at->format('H:i')}}
                                                        </small>
                                                    </div>
                                                    <p class="text-muted mb-2">{{$activity->description}}</p>
                                                    @if($activity->getExtraProperty('link'))
                                                        <a href="{{$activity->getExtraProperty('link')}}" class="btn btn-sm btn-outline-primary">
                                                            {{trans('main.Seemore')}}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-muted mb-0">{{ trans('main.Norecordsfound') }}</p>
                                        @endforelse
                                    </div>
                                    <div class="mt-3">
                                        {{$activities->links()}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div role='tabpanel' class="tab-pane fade" id="notifications">
                            @include('component.list_notifications_profile', ['userName' => $user->name, 'userId' => $user->id, 'notifications' => $notifications])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('component.delete_modal_simple')
@endsection

@section('post_scripts')
    <script src="{{asset('js/profile.js')}}"></script>
@endsection