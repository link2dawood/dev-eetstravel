@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
           ['title' => 'Currency Rates', 'sub_title' => 'Currency Rates List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Currency Rates', 'icon' => null, 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div>
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('currency_rate.create'), \App\CurrencyRate::class) !!}
                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="currency-search" class="form-control" placeholder="Search currency rates..." onkeyup="filterTable('currency-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('currency-table', 'currency_rates_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="currency-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'currency-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'currency-table')">{!!trans('main.Currency')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'currency-table')">{!!trans('main.Rate')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'currency-table')">{!!trans('main.Date')!!} <i class="fa fa-sort"></i></th>
                                <th>{!!trans('main.Actions')!!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($currency_rates as $currency_rate)
                            <tr>
                                <td>{{ $currency_rate->id }}</td>
                                <td>{{ $currency_rate->currency ?? '' }}</td>
                                <td>{{ $currency_rate->rate ?? '' }}</td>
                                <td>{{ $currency_rate->date ?? '' }}</td>
                                <td>
                                    {!! \App\Http\Controllers\DatatablesHelperController::getActionButton([
                                        'show' => route('currency_rate.show', ['currency_rate' => $currency_rate->id]),
                                        'edit' => route('currency_rate.edit', ['currency_rate' => $currency_rate->id]),
                                        'delete_msg' => "/currency_rate/{$currency_rate->id}/deleteMsg"
                                    ], false, $currency_rate) !!}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No currency rates found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {{ $currency_rates->links() }}
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('currency-table');
});
</script>
@endpush