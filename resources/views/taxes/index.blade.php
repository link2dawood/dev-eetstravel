@extends('scaffold-interface.layouts.tabler-app')
@section('title','Index')
@section('content')
    @include('layouts.title',
           ['title' => 'Taxes', 'sub_title' => 'Taxes List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Taxes', 'icon' => null, 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body table-responsive">
                <div>
                    @can('taxes.create')<a class="btn btn-primary" href="{{ route('taxes.create') }}">Create tax</a>@endcan
                </div>
                <br>
                <br>
                @include('component.table_tools', ['tableId' => 'taxes-table', 'label' => 'taxes'])
                <table id="taxes-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>{!!trans('Name')!!}</th>
                        <th>{!!trans('Value')!!}</th>
                        <th class="actions-button" style="width: 140px!important">{!!trans('main.Actions')!!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($taxesData as $tax)
                        <tr>
                            <td>{{ $tax->id }}</td>
                            <td data-delete-label>{{ $tax->name }}</td>
                            <td>{{ $tax->value }}</td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    @include('component.action_buttons', [
                                        'item' => $tax,
                                        'routePrefix' => 'taxes'
                                    ])
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>
        </div>

    </section>
@endsection

@include('component.delete_modal_simple')
