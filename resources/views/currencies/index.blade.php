@extends('scaffold-interface.layouts.tabler-app')
@section('title','Index')
@section('content')
    @include('layouts.title',
           ['title' => 'Currencies', 'sub_title' => 'Currencies List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Currencies', 'icon' => null, 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <div class="input-icon flex-grow-1" style="max-width: 420px;">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        <input type="text" id="currencies-search" class="form-control" placeholder="Search currencies..." name="search" value="{{ request('search') }}" data-list-search>
                    </div>
                    <div class="ms-auto d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-success" onclick="exportTableToCSV('currencies-table', 'currencies_export.csv')">
                            <i class="ti ti-download me-1"></i> Export CSV
                        </button>
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('currencies.create'), \App\Currencies::class) !!}
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="currencies-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'currencies-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'currencies-table')">{!!trans('main.Name')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'currencies-table')">{!!trans('main.Code')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'currencies-table')">{!!trans('main.Symbol')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'currencies-table')">{!!trans('main.Cent')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(5, 'currencies-table')">{!!trans('main.SymbolCent')!!} <i class="fa fa-sort"></i></th>
                                <th>{!!trans('main.Actions')!!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($currencies as $currency)
                            <tr>
                                <td>{{ $currency->id }}</td>
                                <td>{{ $currency->name ?? '' }}</td>
                                <td>{{ $currency->code ?? '' }}</td>
                                <td>{{ $currency->symbol ?? '' }}</td>
                                <td>{{ $currency->cent ?? '' }}</td>
                                <td>{{ $currency->symbol_cent ?? '' }}</td>
                                <td>
                                    {!! \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('currencies.show', ['currency' => $currency->id]),
                                        'edit' => route('currencies.edit', ['currency' => $currency->id]),
                                        'delete_msg' => "/currencies/{$currency->id}/deleteMsg"
                                    ], false, $currency) !!}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No currencies found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {{ $currencies->links() }}
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script src="{{ asset('js/list-search.js') }}"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('currencies-table');
});
</script>
@endpush