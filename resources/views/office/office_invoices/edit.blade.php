@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit office invoice')
@section('content')
<div class="container-xl">
    <h2 class="page-title mb-3">Edit office invoice {{ $invoice->invoice_no }}</h2>
    @if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('office_invoices.update', $invoice->officeinvoice_dataId) }}">
        @csrf
        <div class="card card-body mb-3"><div class="row g-3">
            @foreach(['from_office' => 'From office', 'to_office' => 'To office'] as $field => $label)
            <div class="col-md-3"><label for="{{ $field }}" class="form-label">{{ $label }}</label><select name="{{ $field }}" id="{{ $field }}" class="form-select" required>@foreach($offices as $office)<option value="{{ $office->id }}" {{ old($field, $invoice->$field) == $office->id ? 'selected' : '' }}>{{ $office->office_name }}</option>@endforeach</select></div>
            @endforeach
            <div class="col-md-3"><label for="invoiceno" class="form-label">Invoice number</label><input id="invoiceno" class="form-control" name="invoiceno" value="{{ old('invoiceno', $invoice->invoice_no) }}" required maxlength="55"></div>
            <div class="col-md-3"><label for="dateoffice" class="form-label">Date</label><input id="dateoffice" class="form-control" name="dateoffice" type="date" value="{{ old('dateoffice', $invoice->date) }}" required></div>
        </div></div>
        @foreach($items as $index => $item)
        <div class="card card-body mb-3"><div class="row g-3">
            @foreach(['office_item' => ['Item', $item->officeinvoice_item], 'officeinvoice_date' => ['Date', substr($item->officeinvoice_date, 0, 10)], 'officeinvoice_code' => ['Code', $item->officeinvoice_code], 'officeinvoice_amount' => ['Amount', $item->officeinvoice_amount], 'des' => ['Description', $item->des]] as $field => [$label, $value])
            <div class="col-md"><label class="form-label" for="line-{{ $index }}-{{ $field }}">{{ $label }}</label><input class="form-control" id="line-{{ $index }}-{{ $field }}" name="data[{{ $index }}][{{ $field }}]" value="{{ old('data.'.$index.'.'.$field, $value) }}" type="{{ $field === 'officeinvoice_date' ? 'date' : ($field === 'officeinvoice_amount' ? 'number' : 'text') }}" @if($field === 'officeinvoice_amount') step="0.01" min="0" @endif required></div>
            @endforeach
        </div></div>
        @endforeach
        <div class="btn-list"><button type="submit" class="btn btn-primary">Save</button><a class="btn btn-secondary" href="{{ route('office_invoices_detail.show', $invoice->officeinvoice_dataId) }}">Cancel</a></div>
    </form>
</div>
@endsection
