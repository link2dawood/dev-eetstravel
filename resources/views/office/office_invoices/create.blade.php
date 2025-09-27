@extends('scaffold-interface.layouts.app')

@section('title', 'Create')

@section('content')
    @include('layouts.title', [
        'title' => 'Office Fees',
        'sub_title' => 'Create Office',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'officeInvoices', 'icon' => 'suitcase', 'route' => route('tour.index')],
            ['title' => 'Create', 'route' => null],
        ],
    ])
<style>
	.validation-msg {
  display: block;
  color: red;
  font-size: 12px;
  margin-top: 5px;
}

</style>

    <section class="content">
        <div class="box box-primary">
			<div class="box-body border_top_none">
				 <form method="POST" action="{{ route('officeInvoices.store') }}" id="data-form"  enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-lg-6">
							<label for="officename">From Office</label>
							<select name="from_office" id="from_office" class="form-control select2-hidden-accessible" value="" tabindex="-1" aria-hidden="true" value = "1" disabled>
								<option value="{{ $office_from->id }}" selected>{{$office_from->office_name}}</option>
                            </select>
						</div>
						<div class="col-md-4">
						<div class="form-group">
							<label for="office_id">{{ trans('Office') }}</label>
                                <select name="to_office" id="to_office" class="form-control">

                                     @foreach ($offices_to as $office_to)
									@if($office_to->office_name == $office_from->office_name)
									@else
                                        <option value="{{ $office_to->id }}">{{ $office_to->office_name }}</option>
									@endif
                                    @endforeach
                                </select>
                            </div>
						</div>
						<div class="col-lg-6">
							<label for="dateoffice">Date</label>
                            <input type="date" name="date" id="dateoffice" class="form-control" value="" required>
						</div>
						<div class="col-lg-6">
							<label for="invoiceno">Invoice NO</label>
                            <input type="text" name="invoiceno" id="invoiceno" class="form-control" value="" required>
						</div>
					</div>
			
			</div>
		</div>

	

		
        <div class="box box-primary">
            <div class="box-body border_top_none">
                @if (count($errors) > 0)
                    <br>
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="office_item">Item*</label>
                                <input type="text" name="office_item" id="office_item" class="form-control" value="" required>
                            </div>
                        </div>
						<div class="col-lg-6">
                            <div class="form-group">
                                <label for="des">Description*</label>
                                <input type="text" name="des" id="des" class="form-control" value="" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="officeinvoice_date">Date*</label>
                                <input type="date" name="officeinvoice_date" id="officeinvoice_date" class="form-control" value="" required>
                            </div>
                        </div>
						 <div class="col-lg-6">
                            <div class="form-group">
                                <label for="officeinvoice_code">Item Code*</label>
                                <input type="text" name="officeinvoice_code" id="officeinvoice_code" class="form-control" value="" required>
                            </div>
                        </div>
                    </div>
                     <div class="form-group">
                         <label for="name">Amount*</label>
                         <input type="text" name="officeinvoice_amount" id="officeinvoice_amount" class="form-control" value="" required>
                     </div>
                     <div style="display: flex; align-items-center; justify-content: end; gap: 10px; margin-top: 20px;">
                        <button class="btn btn-success" id="addme" type="button">Add Me</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="box box-primary">
			<div class="box-body border_top_none">
               <table id="offices-table" class="table table-striped table-bordered table-hover" style='background:#fff;; table-layout: fixed ;'>
    <thead>
        <tr>
            <th>id</th>
            <!-- <th>Office Name</th> -->
            <th>Invoice Date</th>
            <th>Item Number</th>
            <th>Desc</th>
            <th>Code</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<div style="display: flex; align-items-center; justify-content: end; gap: 10px; margin-top: 20px;">
    <a href="javascript:history.back()" class="btn btn-primary back_btn">Back</a>
    <button id="submit-btn" class="btn btn-success" type="button">Submit</button>
</div>
			</div>
		</div>
    </section>

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
      '<td>' + newData.id + '</td>' +
      '<td>' + newData.officeinvoice_date + '</td>' +
      '<td>' + newData.office_item + '</td>' +
      '<td>' + newData.des + '</td>' +
      '<td>' + newData.officeinvoice_code + '</td>' +
      '<td>' + newData.officeinvoice_amount + '</td>' +
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
@endsection
	
