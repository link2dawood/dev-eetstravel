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
                <a href="{{url('permissions/create')}}" class="btn btn-success"><i class="fa fa-plus fa-md" aria-hidden="true"></i> {{trans('main.New')}}</a>

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
                                    <a href="{{url('/permissions')}}/{{$permission->id}}/edit" class="btn btn-icon btn-ghost-warning" title="Edit">
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
                                        @endphp
                                        {{-- force=1: the user has seen (and confirmed) which roles lose this permission --}}
                                        <input type="hidden" name="force" value="1">
                                        <button type="submit" class="btn btn-icon btn-ghost-danger js-confirm-delete-form" title="Delete"
                                                data-confirm-message="{{ $permissionRoles !== ''
                                                    ? 'Permission "' . $permission->name . '" is assigned to: ' . $permissionRoles . '. Deleting it will remove this access from those roles. Delete anyway?'
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
        form.dataset.confirmed = 'true';
        form.submit();
    });
});
</script>
@endpush