@extends('scaffold-interface.layouts.tabler-app')
@section('title','Taxes')

@section('post_styles')
    @include('component.datatables_cdn')
@endsection

@section('content')
<x-ui.page-header
    title="Taxes"
    description="Tax rates applied to invoices and quotations."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Taxes'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('taxes.create'))
            <x-ui.button as="a" href="{{ route('taxes.create') }}" icon="plus">New tax</x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

@if(count($taxesData) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="percentage" title="No taxes yet" message="Add your first tax rate to apply it to invoices and quotations.">
            @if(Auth::user()->can('taxes.create'))
                <x-ui.button as="a" href="{{ route('taxes.create') }}" icon="plus">New tax</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="currencies-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff; width: 100%;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">{!! trans('Name') !!}</th>
                        <th class="px-4 py-3">{!! trans('Value') !!}</th>
                        <th class="px-4 py-3 text-right actions-button" style="width: 140px!important">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($taxesData as $tax)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $tax->id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900" data-delete-label>{{ $tax->name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $tax->value }}</td>
                            <td class="px-4 py-3">
                                <div class="btn-list flex-nowrap flex items-center justify-end gap-1">
                                    @include('component.action_buttons', ['item' => $tax, 'routePrefix' => 'taxes'])
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="not"></th>
                        <th>{!! trans('Name') !!}</th>
                        <th>{!! trans('Value') !!}</th>
                        <th class="not"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif
@endsection

@include('component.delete_modal_simple')

@push('scripts')
<script>
    $(document).ready(function () {
        if (!$.fn.DataTable) return;
        let table = $('#currencies-table').DataTable({
            dom: "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                { extend: 'csv',      title: 'Taxes List', exportOptions: { columns: ':not(.actions-button)' } },
                { extend: 'excel',    title: 'Taxes List', exportOptions: { columns: ':not(.actions-button)' } },
                { extend: 'pdfHtml5', title: 'Taxes List', exportOptions: { columns: ':not(.actions-button)' } }
            ],
            pageLength: 50,
            columnDefs: [{ targets: [3], orderable: false }]
        });
        $('#currencies-table tfoot th').each(function () {
            let column = this;
            if (column.className !== 'not') {
                let title = $(this).text();
                $(this).html('<input type="text" class="form-control block w-full h-8 rounded border border-slate-300 bg-white px-2 text-sm" placeholder="Search ' + title + '" />');
            }
        });
        table.columns().every(function () {
            let that = this;
            $('input', this.footer()).on('keyup change', function () {
                if (that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });
        });
        $('#currencies-table tfoot th').appendTo('#currencies-table thead');
    });
</script>
@endpush
