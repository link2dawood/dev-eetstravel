@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Create Office Invoice')

@section('content')
@php
    $inputClass = 'block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600';
    $labelClass = 'block text-sm font-medium text-slate-700 mb-1';
@endphp

<style>
    .validation-msg { display:block; color:#dc2626; font-size:12px; margin-top:5px; }
</style>

<x-ui.page-header
    title="Office Fees"
    description="Record fees and items invoiced between offices."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Office Invoices'],
        ['label' => 'Create'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left" class="back_btn">Back</x-ui.button>
    </x-slot>
</x-ui.page-header>

{{-- Invoice header --}}
<form method="POST" action="{{ route('officeInvoices.store') }}" id="data-form" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="rounded border border-slate-200 bg-white mb-6">
        <div class="border-b border-slate-200 px-5 py-4 flex items-center gap-2">
            <x-ui.icon name="building" size="sm" class="text-slate-400" />
            <h2 class="text-base font-semibold text-slate-900">Invoice Header</h2>
        </div>
        <div class="px-5 py-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <label for="from_office" class="{{ $labelClass }}">From Office</label>
                    <select name="from_office" id="from_office" class="{{ $inputClass }}" disabled>
                        <option value="{{ $office_from->id }}" selected>{{ $office_from->office_name }}</option>
                    </select>
                </div>
                <div>
                    <label for="to_office" class="{{ $labelClass }}">{{ trans('Office') }}</label>
                    <select name="to_office" id="to_office" class="{{ $inputClass }}">
                        @foreach ($offices_to as $office_to)
                            @if($office_to->office_name == $office_from->office_name)
                            @else
                                <option value="{{ $office_to->id }}">{{ $office_to->office_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="dateoffice" class="{{ $labelClass }}">Date</label>
                    <input type="date" name="date" id="dateoffice" class="{{ $inputClass }}" value="" required>
                </div>
                <div>
                    <label for="invoiceno" class="{{ $labelClass }}">Invoice No</label>
                    <input type="text" name="invoiceno" id="invoiceno" class="{{ $inputClass }}" value="" required>
                </div>
            </div>
        </div>
    </div>

    {{-- Add item --}}
    <div class="rounded border border-slate-200 bg-white mb-6">
        <div class="border-b border-slate-200 px-5 py-4 flex items-center gap-2">
            <x-ui.icon name="list" size="sm" class="text-slate-400" />
            <h2 class="text-base font-semibold text-slate-900">Add Item</h2>
        </div>
        <div class="px-5 py-5">
            @if (count($errors) > 0)
                <div class="mb-4 rounded border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-800">
                    <ul class="list-disc pl-5 space-y-1 m-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <label for="office_item" class="{{ $labelClass }}">Item <span class="text-danger-600">*</span></label>
                    <input type="text" name="office_item" id="office_item" class="{{ $inputClass }}" value="" required>
                </div>
                <div>
                    <label for="des" class="{{ $labelClass }}">Description <span class="text-danger-600">*</span></label>
                    <input type="text" name="des" id="des" class="{{ $inputClass }}" value="" required>
                </div>
                <div>
                    <label for="officeinvoice_date" class="{{ $labelClass }}">Date <span class="text-danger-600">*</span></label>
                    <input type="date" name="officeinvoice_date" id="officeinvoice_date" class="{{ $inputClass }}" value="" required>
                </div>
                <div>
                    <label for="officeinvoice_code" class="{{ $labelClass }}">Item Code <span class="text-danger-600">*</span></label>
                    <input type="text" name="officeinvoice_code" id="officeinvoice_code" class="{{ $inputClass }}" value="" required>
                </div>
                <div>
                    <label for="officeinvoice_amount" class="{{ $labelClass }}">Amount <span class="text-danger-600">*</span></label>
                    <input type="text" name="officeinvoice_amount" id="officeinvoice_amount" class="{{ $inputClass }}" value="" required>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <x-ui.button id="addme" type="button" icon="plus" variant="secondary">Add Me</x-ui.button>
            </div>
        </div>
    </div>
</form>

{{-- Added items --}}
<div class="rounded border border-slate-200 bg-white mb-6">
    <div class="border-b border-slate-200 px-5 py-4 flex items-center gap-2">
        <x-ui.icon name="table" size="sm" class="text-slate-400" />
        <h2 class="text-base font-semibold text-slate-900">Items</h2>
    </div>
    <div class="px-5 py-5">
        <div class="overflow-x-auto">
            <table id="offices-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">id</th>
                        <th class="px-4 py-3">Invoice Date</th>
                        <th class="px-4 py-3">Item Number</th>
                        <th class="px-4 py-3">Desc</th>
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100"></tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-end gap-2">
            <a href="javascript:history.back()" class="back_btn inline-flex h-9 items-center rounded border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-50">Back</a>
            <button id="submit-btn" type="button" class="inline-flex h-9 items-center gap-2 rounded bg-primary-600 px-4 text-sm font-medium text-white hover:bg-primary-700">
                Submit
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  var counter = 0; // Counter variable

 $('#addme').on('click', function() {
	var dateoffice = $("#dateoffice").val();
	var invoiceno = $("#invoiceno").val();
    var office_item = $("#office_item").val();
    var des = $("#des").val();
    var officeinvoice_date = $("#officeinvoice_date").val();
    var officeinvoice_code = $("#officeinvoice_code").val();
    var officeinvoice_amount = $("#officeinvoice_amount").val();

    // Check if any field is empty invoiceno
	   if (dateoffice === '') {
      showValidationMessage('dateoffice', 'Please enter the date office.');
      return;
	  }
	  if (invoiceno === '') {
      showValidationMessage('invoiceno', 'Please enter the invoice No.');
      return;
	  }
    if (office_item === '') {
      showValidationMessage('office_item', 'Please enter the office item.');
      return;
    }
    if (des === '') {
      showValidationMessage('des', 'Please enter the description.');
      return;
    }
    if (officeinvoice_date === '') {
      showValidationMessage('officeinvoice_date', 'Please select the office invoice date.');
      return;
    }
    if (officeinvoice_code === '') {
      showValidationMessage('officeinvoice_code', 'Please enter the item code.');
      return;
    }
    if (officeinvoice_amount === '') {
      showValidationMessage('officeinvoice_amount', 'Please enter the office invoice amount.');
      return;
    }

    counter++;

    var newData = {
      id: counter,
      officeinvoice_date: officeinvoice_date,
      office_item: office_item,
      des: des,
      officeinvoice_code: officeinvoice_code,
      officeinvoice_amount: officeinvoice_amount
    };

    var newRow = '<tr>' +
      '<td class="px-4 py-3 text-slate-700">' + newData.id + '</td>' +
      '<td class="px-4 py-3 text-slate-700">' + newData.officeinvoice_date + '</td>' +
      '<td class="px-4 py-3 text-slate-700">' + newData.office_item + '</td>' +
      '<td class="px-4 py-3 text-slate-700">' + newData.des + '</td>' +
      '<td class="px-4 py-3 text-slate-700">' + newData.officeinvoice_code + '</td>' +
      '<td class="px-4 py-3 text-slate-700">' + newData.officeinvoice_amount + '</td>' +
      '</tr>';

    $('#offices-table tbody').append(newRow);

    // Clear the form fields
    $("#office_item").val('');
    $("#des").val('');
    $("#officeinvoice_date").val('');
    $("#officeinvoice_code").val('');
    $("#officeinvoice_amount").val('');

    // Remove the validation message
    removeValidationMessage('office_item');
    removeValidationMessage('des');
    removeValidationMessage('officeinvoice_date');
    removeValidationMessage('officeinvoice_code');
    removeValidationMessage('officeinvoice_amount');
  });

  $('#submit-btn').on('click', function() {
    var tableData = [];

    $('#offices-table tbody tr').each(function() {
      var row = {};
      row['officeinvoice_date'] = $(this).find('td:eq(1)').text();
      row['office_item'] = $(this).find('td:eq(2)').text();
      row['des'] = $(this).find('td:eq(3)').text();
      row['officeinvoice_code'] = $(this).find('td:eq(4)').text();
      row['officeinvoice_amount'] = $(this).find('td:eq(5)').text();
      tableData.push(row);
    });

    // Prepare the data to be sent to the database
    var formData = {
      _token: $('meta[name="csrf-token"]').attr('content'),
      from_office: $('#from_office').val(),
      to_office: $('#to_office').val(),
      dateoffice: $('#dateoffice').val(),
      invoiceno: $('#invoiceno').val(),
      data: tableData
    };

    // Send the table data to the database
    $.ajax({
      url: $('#data-form').attr('action'),
      type: $('#data-form').attr('method'),
      data: formData,
      success: function(result) {
        location.reload();
        console.log(result);
      },
      error: function(result) {
        console.log(result);
      }
    });
  });

  function showValidationMessage(fieldId, message) {
    // Create or update the validation message element
    var validationMsg = $('#' + fieldId + '-validation');
    if (validationMsg.length === 0) {
      validationMsg = $('<span id="' + fieldId + '-validation" class="validation-msg"></span>');
      $('#' + fieldId).after(validationMsg);
    }
    validationMsg.text(message);
  }

  function removeValidationMessage(fieldId) {
    $('#' + fieldId + '-validation').remove();
  }

});
</script>
@endpush
