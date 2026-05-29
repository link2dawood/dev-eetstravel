@extends('scaffold-interface.layouts.tabler-app')
@section('title','Criteria')

@section('post_styles')
    @include('component.datatables_cdn')
@endsection

@section('content')
<x-ui.page-header
    title="Criteria"
    description="Search criteria tags used to filter tours and bookings."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Criteria'],
    ]"
>
    <x-slot name="actions">
        @if(Auth::user()->can('criteria.create'))
            <x-ui.button as="a" href="{{ route('criteria.create') }}" icon="plus">New criteria</x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

@if(Session::has('message'))
    <div class="mb-4 flex items-start gap-3 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <x-ui.icon name="alert-octagon" class="mt-0.5 text-danger-600" />
        <div class="flex-1">{{ Session::get('message') }}</div>
    </div>
@endif

@if(count($criteriaData) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="tag" title="No criteria yet" message="Add your first search criterion to start tagging tours.">
            @if(Auth::user()->can('criteria.create'))
                <x-ui.button as="a" href="{{ route('criteria.create') }}" icon="plus">New criteria</x-ui.button>
            @endif
        </x-ui.empty-state>
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="criteria-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff; width: 98%; table-layout: fixed">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">{!! trans('main.Name') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.ShortName') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Icon') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.CriteriaType') !!}</th>
                        <th class="px-4 py-3 text-right actions-button" style="width: 140px!important">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($criteriaData as $criteria)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $criteria->id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $criteria->name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $criteria->short_name }}</td>
                            <td class="px-4 py-3 text-slate-700">{!! $criteria->formatted_icon !!}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $criteria->criteria_type }}</td>
                            <td class="px-4 py-3"><div class="flex items-center justify-end gap-1">{!! $criteria->action_buttons !!}</div></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="not"></th>
                        <th>{!! trans('main.Name') !!}</th>
                        <th>{!! trans('main.ShortName') !!}</th>
                        <th>{!! trans('main.Icon') !!}</th>
                        <th>{!! trans('main.CriteriaType') !!}</th>
                        <th class="not"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        if (!$.fn.DataTable) return;
        let table = $('#criteria-table').DataTable({
            dom: "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                { extend: 'csv',      title: 'Criteria List', exportOptions: { columns: ':not(.actions-button)' } },
                { extend: 'excel',    title: 'Criteria List', exportOptions: { columns: ':not(.actions-button)' } },
                { extend: 'pdfHtml5', title: 'Criteria List', exportOptions: { columns: ':not(.actions-button)' } }
            ],
            pageLength: 50,
            columnDefs: [{ targets: [5], orderable: false }]
        });
        $('#criteria-table tfoot th').each(function () {
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
        $('#criteria-table tfoot th').appendTo('#criteria-table thead');
    });
</script>
@endpush
