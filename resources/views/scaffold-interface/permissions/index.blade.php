@extends('scaffold-interface.layouts.tabler-app')
@section('content')
    @include('layouts.title', [
        'title' => 'Permissions',
        'sub_title' => 'Permissions List',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Permissions', 'icon' => 'key', 'route' => null]
        ]
    ])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                    <a href="{{url('permissions/create')}}" class="btn btn-success"><i class="fa fa-plus fa-md" aria-hidden="true"></i> {{trans('main.New')}}</a>
                    <form method="GET" action="{{ route('permissions.index') }}" class="d-flex gap-2">
                        <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Search name or alias" aria-label="Search permissions">
                        <button type="submit" class="btn btn-primary">Search</button>
                        @if($search !== '')
                            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Clear</a>
                        @endif
                    </form>
                </div>

<table class="table table-striped mt-3"> {{-- Added mt-3 class for clean spacing --}}
                    <thead>
                    <tr>
                        <th>{{trans('main.Permission')}}</th>
                        <th>{{trans('main.Alias')}}</th>
                        <th style="width: 140px">{{trans('main.Actions')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                        <tr>
                            <td>{{$permission->name}}</td>
                            <td>{{$permission->alias}}</td>
                            <td>
                                <div class="btn-list flex-nowrap table-actions-inline">
                                    <a href="{{url('/permissions')}}/{{$permission->id}}/edit" class="btn btn-icon btn-primary" title="Edit" aria-label="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        @php
                                            $permissionRoles = $permission->roles->pluck('name')->implode(', ');
                                            $usedBy = collect([
                                                $permissionRoles !== '' ? 'roles: ' . $permissionRoles : null,
                                                $permission->users_count ? $permission->users_count . ' user(s)' : null,
                                            ])->filter()->implode(' and ');
                                        @endphp
                                        {{-- force=1 is added by the confirm handler, only after the in-use warning was accepted --}}
                                        <button type="submit" class="btn btn-icon btn-danger js-confirm-delete-form" title="Delete" aria-label="Delete"
                                                data-in-use="{{ $usedBy !== '' ? '1' : '0' }}"
                                                data-confirm-message="{{ $usedBy !== ''
                                                    ? 'Permission "' . $permission->name . '" is assigned to ' . $usedBy . '. Deleting it will remove this access from them. Delete anyway?'
                                                    : 'Are you sure you want to delete this permission?' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-secondary py-4">
                                No permissions found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $permissions->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection

<style>
    
    .table-actions-inline,
    .btn-list.table-actions-inline {
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        gap: 0.5rem;
        flex-wrap: nowrap;
    }

    .table-actions-inline form {
        display: inline-flex !important;
        align-items: center;
        margin: 0;
    }

    .table-actions-inline .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        vertical-align: middle;
    }
.table tbody td {
        vertical-align: middle;
    }
</style>
@push('scripts')
<script>
document.addEventListener('submit', function (event) {
    const form = event.target;
    const button = form.querySelector('.js-confirm-delete-form');
    if (!button || form.dataset.confirmed === 'true') return;

    event.preventDefault();
    const message = button.dataset.confirmMessage || 'Are you sure you want to delete this item?';
    const confirmPromise = typeof window.appConfirm === 'function'
        ? window.appConfirm(message, { title: 'Confirm delete', confirmText: 'Delete', cancelText: 'Cancel' })
        : Promise.resolve(true);

    confirmPromise.then(function (confirmed) {
        if (!confirmed) return;
        if (button.dataset.inUse === '1' && !form.querySelector('input[name="force"]')) {
            const force = document.createElement('input');
            force.type = 'hidden';
            force.name = 'force';
            force.value = '1';
            form.appendChild(force);
        }
        form.dataset.confirmed = 'true';
        form.submit();
    });
});
</script>
@endpush