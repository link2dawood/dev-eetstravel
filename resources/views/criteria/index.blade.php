@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Criteria')
@section('content')
    @include('layouts.title', ['title' => 'Criteria', 'sub_title' => 'Criteria List', 'breadcrumbs' => [
        ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
        ['title' => 'Criteria', 'icon' => null, 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                    <form method="GET" action="{{ route('criteria.index') }}" class="d-flex gap-2">
                        <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name, short name or type" aria-label="Search criteria">
                        <button type="submit" class="btn btn-primary">Search</button>
                        @if(request('search'))<a href="{{ route('criteria.index') }}" class="btn btn-outline-secondary">Clear</a>@endif
                    </form>
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('criteria.create'), \App\Criteria::class) !!}
                </div>
                <div class="table-responsive">
                    <table id="criteria-table" class="table table-striped table-bordered">
                        <thead><tr>
                            <th>ID</th><th>{{ trans('main.Name') }}</th><th>{{ trans('main.ShortName') }}</th>
                            <th>{{ trans('main.Icon') }}</th><th>{{ trans('main.CriteriaType') }}</th><th>{{ trans('main.Actions') }}</th>
                        </tr></thead>
                        <tbody>
                            @forelse($criteriaData as $criteria)
                                <tr>
                                    <td>{{ $criteria->id }}</td><td>{{ $criteria->name }}</td><td>{{ $criteria->short_name }}</td>
                                    <td><i class="fa fa-{{ $criteria->icon }}" aria-hidden="true"></i> {{ $criteria->icon }}</td>
                                    <td>{{ $criteria->criteria_type ?? 'Unknown type' }}</td>
                                    <td>@include('component.action_buttons', ['item' => $criteria, 'routePrefix' => 'criteria'])</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No criteria found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $criteriaData->links() }}
            </div>
        </div>
    </section>
@endsection
