@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Create')

@section('content')
<x-ui.page-header
    title="New office invoice"
    :description="$office_from->office_name ?? null"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Offices', 'href' => route('office.index')],
        ['label' => 'Office invoice'],
        ['label' => 'New'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">Back</x-ui.button>
    </x-slot>
</x-ui.page-header>

@if(count($errors) > 0)
    <div class="mb-4 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <ul class="list-disc pl-5 space-y-0.5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('officeInvoices.store') }}" id="data-form" enctype="multipart/form-data" class="space-y-4">
    {{ csrf_field() }}

    {{-- Header: from / to / date / invoice no --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="receipt" size="sm" /></div>
            <div class="flex-1 min-w-0"><h2 class="text-sm font-medium text-slate-700">Invoice header</h2></div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="from_office" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">From office</label>
                <select name="from_office" id="from_office" disabled
                        class="form-control select2-hidden-accessible block w-full h-9 rounded border border-slate-300 bg-slate-100 px-3 text-sm text-slate-700 shadow-subtle">
                    <option value="{{ $office_from->id }}" selected>{{ $office_from->office_name }}</option>
                </select>
            </div>
            <div>
                <label for="to_office" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('Office') }}</label>
                <select name="to_office" id="to_office"
                        class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    @foreach($offices_to as $office_to)
                        @if($office_to->office_name != $office_from->office_name)
                            <option value="{{ $office_to->id }}">{{ $office_to->office_name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label for="dateoffice" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Date</label>
                <input type="date" name="date" id="dateoffice" required
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="invoiceno" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Invoice no</label>
                <input type="text" name="invoiceno" id="invoiceno" required
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
        </div>
    </div>

    {{-- Line item input --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="package" size="sm" /></div>
            <div class="flex-1 min-w-0"><h2 class="text-sm font-medium text-slate-700">Add line item</h2></div>
        </div>
        <div class="px-5 py-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="office_item" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Item <span class="text-danger-600">*</span></label>
                    <input type="text" name="office_item" id="office_item" required
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="des" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Description <span class="text-danger-600">*</span></label>
                    <input type="text" name="des" id="des" required
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="officeinvoice_date" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Date <span class="text-danger-600">*</span></label>
                    <input type="date" name="officeinvoice_date" id="officeinvoice_date" required
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="officeinvoice_code" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Item code <span class="text-danger-600">*</span></label>
                    <input type="text" name="officeinvoice_code" id="officeinvoice_code" required
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
            </div>
            <div>
                <label for="officeinvoice_amount" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Amount <span class="text-danger-600">*</span></label>
                <input type="text" name="officeinvoice_amount" id="officeinvoice_amount" required
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div class="flex justify-end">
                <x-ui.button type="button" id="addme" icon="plus">Add to invoice</x-ui.button>
            </div>
        </div>
    </div>

    {{-- Items table --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3">
            <h2 class="text-sm font-medium text-slate-700">Invoice items</h2>
        </div>
        <div class="overflow-x-auto">
            <table id="offices-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff; table-layout: fixed;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-3 py-2">ID</th>
                        <th class="px-3 py-2">Invoice date</th>
                        <th class="px-3 py-2">Item number</th>
                        <th class="px-3 py-2">Description</th>
                        <th class="px-3 py-2">Code</th>
                        <th class="px-3 py-2">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100"></tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-3 flex items-center justify-end gap-2 bg-slate-50">
            <x-ui.button as="a" href="javascript:history.back()" variant="secondary">Back</x-ui.button>
            <x-ui.button type="button" id="submit-btn" icon="save">Submit invoice</x-ui.button>
        </div>
    </div>
</form>

<style>.validation-msg { display: block; color: #b91c1c; font-size: 12px; margin-top: 5px; }</style>

<script>
$(document).ready(function () {
    var counter = 0;

    $('#addme').on('click', function () {
        var dateoffice = $("#dateoffice").val();
        var invoiceno = $("#invoiceno").val();
        var office_item = $("#office_item").val();
        var des = $("#des").val();
        var officeinvoice_date = $("#officeinvoice_date").val();
        var officeinvoice_code = $("#officeinvoice_code").val();
        var officeinvoice_amount = $("#officeinvoice_amount").val();

        if (dateoffice === '') { showValidationMessage('dateoffice', 'Please enter the date office.'); return; }
        if (invoiceno === '') { showValidationMessage('invoiceno', 'Please enter the invoice No.'); return; }
        if (office_item === '') { showValidationMessage('office_item', 'Please enter the office item.'); return; }
        if (des === '') { showValidationMessage('des', 'Please enter the description.'); return; }
        if (officeinvoice_date === '') { showValidationMessage('officeinvoice_date', 'Please select the office invoice date.'); return; }
        if (officeinvoice_code === '') { showValidationMessage('officeinvoice_code', 'Please enter the item code.'); return; }
        if (officeinvoice_amount === '') { showValidationMessage('officeinvoice_amount', 'Please enter the office invoice amount.'); return; }

        counter++;
        var newRow = '<tr class="hover:bg-slate-50">' +
            '<td class="px-3 py-2 font-mono text-xs text-slate-500">#' + counter + '</td>' +
            '<td class="px-3 py-2 text-slate-700">' + officeinvoice_date + '</td>' +
            '<td class="px-3 py-2 text-slate-700">' + office_item + '</td>' +
            '<td class="px-3 py-2 text-slate-700">' + des + '</td>' +
            '<td class="px-3 py-2 font-mono text-slate-700">' + officeinvoice_code + '</td>' +
            '<td class="px-3 py-2 font-mono text-slate-700">' + officeinvoice_amount + '</td>' +
            '</tr>';
        $('#offices-table tbody').append(newRow);

        $("#office_item").val('');
        $("#des").val('');
        $("#officeinvoice_date").val('');
        $("#officeinvoice_code").val('');
        $("#officeinvoice_amount").val('');

        ['office_item', 'des', 'officeinvoice_date', 'officeinvoice_code', 'officeinvoice_amount'].forEach(removeValidationMessage);
    });

    $('#submit-btn').on('click', function () {
        var tableData = [];
        $('#offices-table tbody tr').each(function () {
            var row = {};
            row['officeinvoice_date'] = $(this).find('td:eq(1)').text();
            row['office_item'] = $(this).find('td:eq(2)').text();
            row['des'] = $(this).find('td:eq(3)').text();
            row['officeinvoice_code'] = $(this).find('td:eq(4)').text();
            row['officeinvoice_amount'] = $(this).find('td:eq(5)').text();
            tableData.push(row);
        });

        var formData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            from_office: $('#from_office').val(),
            to_office: $('#to_office').val(),
            dateoffice: $('#dateoffice').val(),
            invoiceno: $('#invoiceno').val(),
            data: tableData
        };

        $.ajax({
            url: $('#data-form').attr('action'),
            type: $('#data-form').attr('method'),
            data: formData,
            success: function () { location.reload(); },
            error: function (result) { console.log(result); }
        });
    });

    function showValidationMessage(fieldId, message) {
        var validationMsg = $('#' + fieldId + '-validation');
        if (validationMsg.length === 0) {
            validationMsg = $('<span id="' + fieldId + '-validation" class="validation-msg"></span>');
            $('#' + fieldId).after(validationMsg);
        }
        validationMsg.text(message);
    }
    function removeValidationMessage(fieldId) { $('#' + fieldId + '-validation').remove(); }
});
</script>
@endsection
