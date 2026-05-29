@extends('scaffold-interface.layouts.tabler-app')
@section('title','Edit Profile')

@section('content')
<x-ui.page-header
    title="Edit profile"
    :description="$user->name"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Profile', 'href' => url('profile')],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ url('profile') }}" variant="ghost" icon="arrow-left">{{ trans('main.Back') }}</x-ui.button>
    </x-slot>
</x-ui.page-header>

@if(count($errors) > 0)
    <div class="mb-4 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <ul class="list-disc pl-5 space-y-0.5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ url('/users/'.$user->id) }}" method="post" enctype="multipart/form-data" class="space-y-4">
    {!! csrf_field() !!}
    <input type="hidden" name="user_id" value="{{ $user->id }}">
    <input type="hidden" name="edit_profile" value="1">

    {{-- Section: Profile --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="user" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Profile details</h2>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Email') }} <span class="text-danger-600">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Name') }} <span class="text-danger-600">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Password') }}</label>
                <input type="password" name="password" placeholder="Leave blank to keep current password"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                <p class="mt-1 text-xs text-slate-500">Only fill this if you want to change your password.</p>
            </div>
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.EmailLogin') }} <span class="text-slate-400 normal-case font-normal">(SnappyMail)</span></label>
                <input type="email" name="email_login" id="email_login" value="{{ old('email_login', $user->email_login) }}" placeholder="user@example.com"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                <p class="mt-1 text-xs text-slate-500">Used to connect the user's SnappyMail inbox.</p>
            </div>
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Email') }} {{ trans('main.Password') }} <span class="text-slate-400 normal-case font-normal">(SnappyMail)</span></label>
                <input type="password" name="email_password" id="email_password" placeholder="Enter new SnappyMail password"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                <p class="mt-1 text-xs text-slate-500">Leave blank to keep the existing SnappyMail password.</p>
            </div>
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Education') }}</label>
                <input type="text" name="education" value="{{ old('education', $user->education) }}" placeholder="Education"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Location') }}</label>
                <input type="text" name="location" value="{{ old('location', $user->location) }}" placeholder="Location"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Note') }}</label>
                <input type="text" name="note" value="{{ old('note', $user->note) }}" placeholder="Note"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-2">Avatar</label>
                <div class="flex items-center gap-4">
                    <img src="{{ $user->avatar ? asset($user->avatar) : asset('img/avatar.png') }}" alt="Avatar"
                         class="rounded-full border border-slate-200" style="width: 80px; height: 80px; object-fit: cover;" />
                    <input id="avatar" name="avatar" type="file" accept="image/*" data-show-upload="false"
                           class="file block w-full text-sm text-slate-700 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-primary-600 file:text-white file:cursor-pointer hover:file:bg-primary-700" />
                </div>
                <p class="mt-1 text-xs text-slate-500">Upload a new avatar image (optional).</p>
            </div>
        </div>
    </div>

    <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
        <x-ui.button as="a" href="{{ url('profile') }}" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
        <x-ui.button type="submit" variant="primary" icon="save">{{ trans('main.Save') }}</x-ui.button>
    </div>
</form>

@if(Auth::user()->hasRole('admin'))
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        {{-- Roles --}}
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
                <x-ui.icon name="shield" size="sm" class="text-slate-400" />
                <h2 class="text-sm font-medium text-slate-700">{{ $user->name }} — {{ trans('main.Roles') }}</h2>
            </div>
            <div class="px-5 py-5">
                <form action="{{ url('users/addRole') }}" method="post" class="flex items-center gap-2 mb-4">
                    {!! csrf_field() !!}
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <select name="role_name"
                            class="form-control block flex-1 h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        @foreach($roles as $key => $role)
                            <option value="{{ $role }}">{{ $role }}</option>
                        @endforeach
                    </select>
                    <button class="inline-flex h-9 items-center gap-1 rounded bg-primary-600 px-3 text-sm font-medium text-white hover:bg-primary-700">{{ trans('main.Addrole') }}</button>
                </form>
                <div class="rounded border border-slate-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                <th class="px-3 py-2">{{ trans('main.Role') }}</th>
                                <th class="px-3 py-2 text-right">{{ trans('main.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($userRoles as $role)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-3 py-2 text-slate-700">{{ $role }}</td>
                                    <td class="px-3 py-2 text-right">
                                        <form action="{{ route('user.remove_role') }}" method="POST" class="inline">
                                            {{ csrf_field() }}
                                            <input type="text" hidden name="user_id" value="{{ $user->id }}">
                                            <input type="text" hidden name="role" value="{{ $role }}">
                                            <button type="submit" class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700">
                                                <x-ui.icon name="trash-2" size="sm" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Permissions --}}
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
                <x-ui.icon name="key" size="sm" class="text-slate-400" />
                <h2 class="text-sm font-medium text-slate-700">{{ $user->name }} — {{ trans('main.Permissions') }}</h2>
            </div>
            <div class="px-5 py-5">
                <form action="{{ url('users/addPermission') }}" method="post" class="space-y-3 mb-4">
                    {!! csrf_field() !!}
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <select name="permission_name[]" multiple
                            class="js-state form-control select22 block w-full rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        @foreach($permissions as $key => $permission)
                            <option value="{{ $key }}">{{ $permission }}</option>
                        @endforeach
                    </select>
                    <button class="inline-flex h-9 items-center gap-1 rounded bg-primary-600 px-3 text-sm font-medium text-white hover:bg-primary-700">{{ trans('main.Addpermission') }}</button>
                </form>
                <div class="rounded border border-slate-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                <th class="px-3 py-2">{{ trans('main.Permission') }}</th>
                                <th class="px-3 py-2 text-right">{{ trans('main.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($userPermissions as $key => $permission)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-3 py-2 text-slate-700">{{ $permission }}</td>
                                    <td class="px-3 py-2 text-right">
                                        <a href="{{ url('users/removePermission') }}/{{ $user->id }}/{{ $key }}"
                                           class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-500 hover:bg-danger-50 hover:text-danger-700">
                                            <x-ui.icon name="trash-2" size="sm" />
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('post_scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select22').select2({ placeholder: "Select permissions", allowClear: true });
    });
</script>
@endsection
