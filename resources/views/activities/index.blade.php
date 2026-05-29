@extends('scaffold-interface.layouts.tabler-app')
@section('title','Activities')

@section('post_styles')
    @include('component.datatables_cdn')
@endsection

@section('content')
<x-ui.page-header
    title="Activities"
    description="System audit log — who did what, and when."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Activities'],
    ]"
/>

@if(count($activitiesData) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="history" title="No activity yet" message="Recent user actions will appear here once the audit log captures them." />
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="activity-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">{!! trans('main.CreatedTime') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.UserCreated') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Description') !!}</th>
                        <th class="px-4 py-3 text-right actions-button" style="width: 140px">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($activitiesData as $activity)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-xs font-mono text-slate-500 whitespace-nowrap">{{ $activity->created_at }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $activity->causer }}</td>
                            <td class="px-4 py-3 text-slate-800" data-delete-label>{{ $activity->formatted_description }}</td>
                            <td class="px-4 py-3">
                                <div class="btn-list flex-nowrap flex items-center justify-end gap-1">
                                    @if(!empty($activity->link_button))
                                        {!! $activity->link_button !!}
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>{!! trans('main.CreatedTime') !!}</th>
                        <th>{!! trans('main.UserCreated') !!}</th>
                        <th>{!! trans('main.Description') !!}</th>
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
        let table = $('#activity-table').DataTable({
            dom: "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                { extend: 'csv',      title: 'Activities List', exportOptions: { columns: ':not(.actions-button)' } },
                { extend: 'excel',    title: 'Activities List', exportOptions: { columns: ':not(.actions-button)' } },
                { extend: 'pdfHtml5', title: 'Activities List', exportOptions: { columns: ':not(.actions-button)' } }
            ],
            language: { search: 'Global Search :' },
            pageLength: 50,
            columnDefs: [{ targets: [3], orderable: false }]
        });
        $('#activity-table tfoot th').each(function () {
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
        $('#activity-table tfoot th').appendTo('#activity-table thead');
    });
</script>
@endpush
