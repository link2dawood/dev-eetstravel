@extends('scaffold-interface.layouts.tabler-app')
@section('title','Users')
@section('content')
    @include('layouts.title', [
        'title' => 'Users',
        'sub_title' => 'Users List',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Users', 'icon' => 'user', 'route' => null]
        ]
    ])

    <section class="content">
        <div class="card">
            <div class="card-body">
                @if (Session::has('message'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 9v2m0 4v2" />
                        </svg>
                        {{ Session::get('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="mb-3">
                    <a href="{{ url('/users/create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        {{ trans('main.New') }}
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-vcenter card-table clickable-rows">
                        <thead>
                            <tr>
                                <th>{{ trans('main.Name') }}</th>
                                <th>{{ trans('main.Email') }}</th>
                                <th>{{ trans('main.Roles') }}</th>
                                <th>{{ trans('main.Permissions') }}</th>
                                <th class="w-1">{{ trans('main.Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr class="clickable-row" onclick="window.location.href='{{ url('/users/'.$user->id.'/edit') }}'">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <span class="badge bg-blue-lt">{{ $user->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-secondary">{{ $user->email }}</span>
                                </td>
                                <td>
                                    @if(!empty($user->roles))
                                        <div class="badges-list">
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-blue text-blue-fg">{{ $role->name }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">{{ trans('main.NoRoles') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($user->permissions))
                                        <div class="badges-list">
                                            @foreach($user->permissions as $permission)
                                                <span class="badge bg-orange text-orange-fg">{{ $permission->alias }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">{{ trans('main.NoPermissions') }}</span>
                                    @endif
                                </td>
                                <td onclick="event.stopPropagation();">
                                    @include('component.action_buttons', [
                                        'item' => $user,
                                        'routePrefix' => 'users'
                                    ])
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">
                                    No users found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($users, 'links'))
                    <div class="card-footer">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

<style>
    .clickable-rows .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .clickable-rows .clickable-row:hover {
        background-color: #f8f9fa !important;
    }

    .clickable-rows .clickable-row td:last-child {
        cursor: default;
    }

    .badges-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .badges-list .badge {
        margin: 0;
    }
</style>