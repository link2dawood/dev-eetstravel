@extends('scaffold-interface.layouts.tabler-app')
@section('title','Show')

@section('content')
<x-ui.page-header
    :title="$user->name"
    description="User profile"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => trans('main.Userprofile')],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ url('profile/edit') }}" variant="secondary" icon="edit">{{ trans('main.Edit') }}</x-ui.button>
    </x-slot>
</x-ui.page-header>

@php
    $tabBase   = 'group inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 pb-3 pt-3 text-sm transition-colors border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300';
    $tabActive = '[&.active]:border-primary-600 [&.active]:text-primary-700 [&.active]:font-medium';
    $tabClass  = $tabBase . ' ' . $tabActive;
@endphp

<div class="rounded border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-1">
        <ul class="nav nav-tabs nav-tabs-underline -mb-px flex flex-nowrap gap-6 overflow-x-auto border-0 px-3 list-none pl-0 m-0 [&_.nav-link]:cursor-pointer" data-bs-toggle="tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active {{ $tabClass }}" href="#info_profile" role="tab" data-bs-toggle="tab" aria-selected="true">
                    <x-ui.icon name="user" />{{ trans('main.Info') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $tabClass }}" href="#timeline" role="tab" data-bs-toggle="tab" aria-selected="false" tabindex="-1">
                    <x-ui.icon name="history" />{{ trans('main.History') }}
                </a>
            </li>
            @if(Auth::user()->can('task.index'))
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $tabClass }}" href="#history_tasks_tab" role="tab" data-bs-toggle="tab" id="history-tasks-tab" aria-selected="false" tabindex="-1">
                        <x-ui.icon name="check-square" />{{ trans('main.Tasks') }}
                    </a>
                </li>
            @endif
            @if(Auth::user()->can('tour.index'))
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $tabClass }}" href="#history_tours_tab" role="tab" data-bs-toggle="tab" id="history-tours-tab" aria-selected="false" tabindex="-1">
                        <x-ui.icon name="plane" />{{ trans('main.Tours') }}
                    </a>
                </li>
            @endif
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $tabClass }}" href="#notifications" role="tab" data-bs-toggle="tab" id="notifications-tab" aria-selected="false" tabindex="-1">
                    <x-ui.icon name="bell" />{{ trans('main.Notifications') }}
                </a>
            </li>
        </ul>
    </div>

    <div class="p-5">
        <div class="tab-content">

            {{-- Info tab --}}
            <div class="tab-pane fade show active" id="info_profile">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    {{-- Profile sidebar --}}
                    <div class="rounded border border-slate-200 bg-white">
                        <div class="px-5 py-6 text-center">
                            <img class="rounded-full mx-auto mb-3 border border-slate-200" width="120" height="120"
                                 src="{{ $user->avatar ? asset($user->avatar) : asset('img/avatar.png') }}" alt="{{ $user->name }}" />
                            <h3 class="text-lg font-semibold text-slate-900 mb-0">{{ $user->name }}</h3>
                            <p class="text-sm text-slate-500 mb-4">{{ $user->email }}</p>
                            <dl class="space-y-3 text-left">
                                <div>
                                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-0.5 flex items-center gap-1"><x-ui.icon name="graduation-cap" size="xs" />{{ trans('main.Education') }}</dt>
                                    <dd class="text-sm text-slate-700">{{ $user->education ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-0.5 flex items-center gap-1"><x-ui.icon name="map-pin" size="xs" />{{ trans('main.Location') }}</dt>
                                    <dd class="text-sm text-slate-700">{{ $user->location ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-0.5 flex items-center gap-1"><x-ui.icon name="file-text" size="xs" />{{ trans('main.Notes') }}</dt>
                                    <dd class="text-sm text-slate-700">{{ $user->note ?? '—' }}</dd>
                                </div>
                            </dl>
                            <hr class="my-4 border-slate-200" />
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-2 text-left">{{ trans('main.Updateemails') }}</p>
                            <div class="grid grid-cols-3 gap-2 mb-3">
                                <input type="number" min="1" placeholder="5" id="time_period"
                                       class="form-control block h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                                <select name="update" id="period_type"
                                        class="form-control block h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                                    <option value="D">{{ trans('main.Days') }}</option>
                                    <option value="H">{{ trans('main.Hours') }}</option>
                                </select>
                                <button type="button" id="period_submit" class="inline-flex h-9 items-center justify-center rounded bg-primary-600 px-3 text-sm font-medium text-white hover:bg-primary-700">{{ trans('main.Go') }}</button>
                            </div>
                            <div id="alert-message" class="hidden flex items-center gap-2 rounded border border-info-600/20 bg-info-50 px-3 py-2 text-sm text-info-700">
                                <x-ui.icon name="info" size="sm" class="text-info-600" />
                                <span>{{ trans('main.Theprocessisrunning') }}</span>
                            </div>
                        </div>
                        <div class="border-t border-slate-200 px-5 py-3">
                            <button id="logout" onclick="event.preventDefault(); logoutForm();"
                                    class="inline-flex w-full h-9 items-center justify-center gap-2 rounded border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                <x-ui.icon name="log-out" size="sm" />{{ trans('main.Signout') }}
                            </button>
                            <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="hidden">{{ csrf_field() }}</form>
                        </div>
                    </div>

                    {{-- Edit profile form --}}
                    <div class="lg:col-span-2 rounded border border-slate-200 bg-white">
                        <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
                            <x-ui.icon name="user" size="sm" class="text-slate-400" />
                            <h2 class="text-sm font-medium text-slate-700">Profile details</h2>
                        </div>
                        <div class="px-5 py-5">
                            @if(count($errors) > 0)
                                <div class="mb-4 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
                                    <ul class="list-disc pl-5 space-y-0.5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                                </div>
                            @endif
                            @if(session('incorrect_data'))
                                <div class="mb-4 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700 text-center">{{ session('incorrect_data') }}</div>
                            @endif
                            <form action="{{ url('/users/'.$user->id) }}" method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {!! csrf_field() !!}
                                <input type="hidden" name="user_id" value="{{ $user->id }}">

                                <div>
                                    <label for="email" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Email') }}</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" placeholder="Email" required
                                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                                </div>
                                <div>
                                    <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Name') }}</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" placeholder="Name" required
                                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                                </div>
                                <div>
                                    <label for="password" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Password') }}</label>
                                    <input type="password" name="password" placeholder="••••••"
                                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                                    <p class="mt-1 text-xs text-slate-500">{{ __('Leave blank to keep current password') }}</p>
                                </div>
                                <div></div>
                                <div>
                                    <label for="email_login" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.EmailLogin') }}</label>
                                    <input type="email" name="email_login" id="email_login" value="{{ old('email_login', $user->email_login) }}" placeholder="user@example.com"
                                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                                    <p class="mt-1 text-xs text-slate-500">{{ __('Used for SnappyMail connection') }}</p>
                                </div>
                                <div>
                                    <label for="email_password" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Email') }} {{ trans('main.Password') }}</label>
                                    <input type="password" name="email_password" id="email_password" placeholder="{{ __('Enter new SnappyMail password') }}"
                                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                                    <p class="mt-1 text-xs text-slate-500">{{ __('Leave blank to keep the existing password.') }}</p>
                                </div>
                                <div>
                                    <label for="education" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Education') }}</label>
                                    <input type="text" name="education" value="{{ old('education', $user->education) }}" placeholder="Education"
                                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                                </div>
                                <div>
                                    <label for="location" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Location') }}</label>
                                    <input type="text" name="location" value="{{ old('location', $user->location) }}" placeholder="Location"
                                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                                </div>
                                <div class="md:col-span-2">
                                    <label for="note" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Note') }}</label>
                                    <input type="text" name="note" value="{{ old('note', $user->note) }}" placeholder="Note"
                                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                                </div>
                                <div class="md:col-span-2">
                                    <label for="avatar" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Image') }}</label>
                                    <input id="avatar" name="avatar" type="file"
                                           class="block w-full text-sm text-slate-700 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-primary-600 file:text-white file:cursor-pointer hover:file:bg-primary-700" />
                                </div>
                                <input type="hidden" name="edit_profile" value="1">
                                <div class="md:col-span-2 flex justify-end">
                                    <x-ui.button type="submit" variant="primary" icon="save">{{ trans('main.Submit') }}</x-ui.button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tasks tab --}}
            <div role="tabpanel" class="tab-pane fade" id="history_tasks_tab">
                @include('component.list_tasks_for_profile', ['userName' => $user->name, 'userId' => $user->id, 'tasks' => $tasks])
            </div>

            {{-- Tours tab --}}
            <div role="tabpanel" class="tab-pane fade" id="history_tours_tab">
                @include('component.list_tours_for_profile', ['userName' => $user->name, 'userId' => $user->id, 'tours' => $tours])
            </div>

            {{-- History tab --}}
            <div class="tab-pane fade" id="timeline">
                <div class="space-y-4">
                    @forelse($activities as $activity)
                        <div class="rounded border border-slate-200 bg-white px-4 py-3">
                            <div class="flex items-start justify-between gap-2 flex-wrap mb-1">
                                <h3 class="text-sm font-semibold text-slate-900">{{ $activity->log_name }}</h3>
                                <span class="text-xs text-slate-500 inline-flex items-center gap-1">
                                    <x-ui.icon name="clock" size="xs" />{{ $activity->updated_at->format('d-m-Y H:i') }}
                                </span>
                            </div>
                            <p class="text-sm text-slate-600 mb-2">{{ $activity->description }}</p>
                            @if($activity->getExtraProperty('link'))
                                <a href="{{ $activity->getExtraProperty('link') }}" class="inline-flex items-center gap-1 text-xs font-medium text-primary-700 hover:text-primary-800">
                                    {{ trans('main.Seemore') }}<x-ui.icon name="arrow-right" size="xs" />
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">{{ trans('main.Norecordsfound') }}</p>
                    @endforelse
                </div>
                @if(method_exists($activities, 'links'))
                    <div class="mt-4">{{ $activities->links() }}</div>
                @endif
            </div>

            {{-- Notifications tab --}}
            <div role="tabpanel" class="tab-pane fade" id="notifications">
                @include('component.list_notifications_profile', ['userName' => $user->name, 'userId' => $user->id, 'notifications' => $notifications])
            </div>

        </div>
    </div>
</div>

@include('component.delete_modal_simple')
@endsection

@section('post_scripts')
    <script src="{{ asset('js/profile.js') }}"></script>
@endsection
