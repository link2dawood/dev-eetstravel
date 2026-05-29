@extends('TMSClient.layouts.app')
@section('title', 'Your Tours — TMS Client')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Your tours</h1>
            <p class="mt-1 text-sm text-slate-500">All tours requested and managed in your portal.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" data-bs-toggle="modal" data-bs-target="#new_audience"
                    class="inline-flex h-9 items-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                Upload tour
            </button>
            <a href="{{ url('TMS-Client-simpletours/create') }}"
               class="inline-flex h-9 items-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Simple tour
            </a>
            <a href="{{ url('TMS-Client-tours/create') }}"
               class="inline-flex h-9 items-center gap-2 rounded-md bg-primary-600 px-3 text-sm font-medium text-white hover:bg-primary-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                New tour
            </a>
        </div>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="tour-table" class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">{!! trans('main.Name') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Depdate') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Status') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.ExternalName') !!}</th>
                        <th class="actions-button px-4 py-3 text-right">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th class="not"></th>
                        <th>{!! trans('main.Name') !!}</th>
                        <th>{!! trans('main.Depdate') !!}</th>
                        <th class="select_search">{!! trans('main.Status') !!}</th>
                        <th>{!! trans('main.ExternalName') !!}</th>
                        <th class="not"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>

{{-- Upload Tour modal (Bootstrap modal — kept as-is so the existing JS opens it via data-bs-toggle) --}}
<div class="modal fade" id="new_audience" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Upload Tour</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <a href="{{ url('TMS-Client/downloadSampleExcel') }}" class="inline-flex h-9 items-center gap-2 rounded-md bg-primary-600 px-3 text-sm font-medium text-white hover:bg-primary-700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    Download sample
                </a>
                <div class="input-wrapper mt-5 d-none">
                    <select class="form-control form-select put-attributes" aria-label="Default select example" required>
                        <option selected>Choose email column from CSV</option>
                    </select>
                </div>
                <p class="mt-4 text-sm text-slate-600">Please upload a file in the format shown in the sample.</p>
                <div id="drag_and_drop" class="rounded-md border-2 border-dashed border-slate-300 bg-slate-50 p-6 text-center cursor-pointer hover:border-primary-500 hover:bg-primary-50/30 transition-colors">
                    <label for="fileUploader" class="cursor-pointer flex flex-col items-center gap-2">
                        <div class="h-12 w-12 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                        </div>
                        <span class="text-sm text-slate-700">Drag and drop your <code>.xlsx</code> here, or click to browse</span>
                    </label>
                    <input type="file" id="fileUploader" class="hidden" accept=".xlsx">
                </div>
                <div id="file_wrapper" class="hidden mt-3 rounded-md border border-slate-200 bg-white p-3 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="file-title text-sm font-medium text-slate-800 truncate"></h4>
                        <p class="file-size text-xs text-slate-500 mt-0.5"></p>
                    </div>
                    <a href="#" class="remove-btn inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:text-danger-700 hover:bg-danger-50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </a>
                </div>
                <button type="button" onclick="upload_audience()"
                        class="mt-4 inline-flex w-full h-10 items-center justify-center gap-2 rounded-md bg-primary-600 px-4 text-sm font-medium text-white hover:bg-primary-700">
                    Upload
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    let permission = $('#permission').attr('data-permission');
    let classNameStatus = permission ? 'touredit-status' : '';
    let table = $('#tour-table').DataTable({
        dom: "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'csv',      title: 'Tours List', exportOptions: { columns: ':not(.actions-button)' } },
            { extend: 'excel',    title: 'Tours List', exportOptions: { columns: ':not(.actions-button)' } },
            { extend: 'pdfHtml5', title: 'Tours List', exportOptions: { columns: ':not(.actions-button)' } },
        ],
        language: { search: "Global Search :" },
        processing: true,
        serverSide: true,
        pageLength: 50,
        ajax: { url: "{{ route('client_tour_data') }}" },
        columns: [
            { data: 'id',               name: 'id' },
            { data: 'name',             name: 'name',             className: 'touredit-name' },
            { data: 'departure_date',   name: 'departure_date',   className: 'touredit-departure_date' },
            { data: 'status_name',      className: classNameStatus },
            { data: 'external_name',    name: 'external_name',    className: 'touredit-external_name' },
            { data: 'action',           name: 'action', searchable: false, sorting: false, orderable: false },
        ],
        columnDefs: [{
            targets: 3,
            createdCell: function (td, cellData) {
                if (cellData === 'Requested') $(td).addClass('status pending');
                else if (cellData === 'Cancelled') $(td).addClass('status rejected');
            }
        }],
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                if (column.footer().className === 'select_search') {
                    var select = $('<select class="form-control"><option value=""></option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    column.data().unique().sort().each(function (d) {
                        select.append('<option value="' + d + '">' + d + '</option>');
                    });
                }
            });
        }
    });
    $('#tour-table tfoot th').each(function () {
        if (this.className !== 'not') {
            let title = $(this).text();
            $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
        } else {
            $(this).html('<span> </span>');
        }
    });
    table.columns().every(function () {
        let that = this;
        $('input', this.footer()).on('keyup change', function () {
            if (that.search() !== this.value) that.search(this.value).draw();
        });
    });
    $('#tour-table tfoot th').appendTo('#tour-table thead');
});

function upload_audience() {
    var form_data = new FormData();
    var put_attributes = $('.put-attributes').val();
    var property = document.getElementById('fileUploader').files[0];
    form_data.append("file", property);
    form_data.append("put_attributes", put_attributes);

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    $.ajax({
        type: "POST",
        url: 'uploadTourFile',
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        success: function () {
            Swal.fire({ icon: 'success', title: 'Tour created', text: 'Tour has been created successfully.' }).then(function (r) {
                if (r.isConfirmed) window.location.href = "{{ url('TMS-Client/quotation_requests') }}";
            });
        },
        error: function () {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!' });
        }
    });
}

$('#fileUploader').change(function (e) {
    if (document.getElementById("fileUploader").value.toLowerCase().lastIndexOf(".xlsx") === -1) {
        alert("Please upload a file with .xlsx extension.");
        return false;
    } else {
        var property1 = document.getElementById('fileUploader').files[0];
        var form_data_ = new FormData();
        form_data_.append("fileget", property1);
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.ajax({
            url: 'file_viewer',
            method: 'POST',
            data: form_data_,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                var obj = eval(data);
                $html = "<option selected>Select Option</option>";
                $('.put-attributes').empty();
                for (var i = 0; i < obj[0].length; i++) {
                    $html += "<option value='" + i + "'>" + obj[0][i] + "</option>";
                }
                $('.put-attributes').html($html);
            }
        });
        document.getElementById('file_wrapper').classList.remove('hidden');
        document.querySelector('.file-title').textContent = e.target.files[0].name;
    }
});
</script>
@endpush
