@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit role')

@section('content')
<x-ui.page-header
    title="Role"
    :description="$role->name"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Roles', 'href' => url('roles')],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left" class="back_btn">
            {{ trans('main.Back') }}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

<div class="space-y-4">

    @if (isset($errors) && $errors->any())
        <div class="rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
            <div class="flex items-center gap-2 font-medium mb-1">
                <x-ui.icon name="alert-octagon" class="text-danger-600" />
                Please correct the following:
            </div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Role rename --}}
    <div class="rounded border border-slate-200 bg-white shadow-subtle">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
            <x-ui.icon name="shield-check" class="text-primary-600" />
            <h2 class="text-sm font-semibold text-slate-900">Role name</h2>
        </div>
        <form action="{{ url('roles/update') }}" method="post" class="px-5 py-5 space-y-4">
            {!! csrf_field() !!}
            <input type="hidden" name="role_id" value="{{ $role->id }}">

            <div>
                <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Name</label>
                <input id="name"
                       name="name"
                       type="text"
                       placeholder="Role name"
                       value="{{ $role->name }}"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200 -mx-5 px-5 -mb-5 pb-5 bg-slate-50 rounded-b">
                <x-ui.button as="a" :href="\App\Helper\AdminHelper::getBackButton(route('roles.index'))" variant="secondary">
                    {{ trans('main.Cancel') }}
                </x-ui.button>
                <button type="submit" class="btn btn-success inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm font-medium text-white hover:bg-primary-700">
                    <x-ui.icon name="save" size="sm" />
                    {{ trans('main.Save') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Role permissions --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="rounded border border-slate-200 bg-white shadow-subtle">
            <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
                <x-ui.icon name="key" class="text-primary-600" />
                <h3 class="text-sm font-semibold text-slate-900">{{ $role->name }} — {{ trans('main.Permissions') }}</h3>
            </div>

            <form action="{{ url('roles/addPermission') }}" method="post" class="px-5 py-4 space-y-3 border-b border-slate-200">
                {!! csrf_field() !!}
                <input type="hidden" name="role_id" value="{{ $role->id }}">

                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Add permission</label>
                    {{-- .select22 class hook preserved — script at bottom initialises Select2 on it --}}
                    <select name="permission_name[]"
                            class="js-state form-control select22 block w-full rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle"
                            multiple="multiple">
                        @foreach($permissions as $key => $permission)
                            <option value="{{ $key }}">{{ $permission }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary inline-flex items-center gap-1.5 rounded bg-primary-600 px-3 h-9 text-sm font-medium text-white hover:bg-primary-700">
                        <x-ui.icon name="plus" size="sm" />
                        {{ trans('main.Addpermission') }}
                    </button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="table w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-3">{{ trans('main.Permission') }}</th>
                            <th class="px-5 py-3 text-right" style="width: 80px">{{ trans('main.Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rolePermissions as $key => $permission)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 text-sm font-medium text-slate-900">{{ $permission }}</td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ url('roles/removePermission') . '/' . $key . '/' . $role->id }}"
                                       class="btn btn-danger btn-sm inline-flex h-8 w-8 items-center justify-center rounded border border-slate-300 bg-white text-danger-700 hover:bg-danger-50"
                                       title="Remove permission">
                                        <x-ui.icon name="trash-2" size="sm" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-8 text-center text-sm text-slate-500 italic">
                                    No permissions assigned to this role yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select22').select2({
            placeholder: "Select permissions",
            allowClear: true
        });
    });
</script>
@endsection
