@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Create Service Transaction')

@section('content')
@php
    $inputClass = 'block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600';
    $labelClass = 'block text-sm font-medium text-slate-700 mb-1';
@endphp

<x-ui.page-header
    title="Customer Transaction"
    description="Create a billing transaction for this tour."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Client Invoices', 'href' => route('accounting.index')],
        ['label' => 'Create Billing'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left" class="back_btn">{!! trans('main.Back') !!}</x-ui.button>
        <x-ui.button type="submit" form="service-transaction-form" icon="check">{!! trans('main.Save') !!}</x-ui.button>
    </x-slot>
</x-ui.page-header>

<form method='POST' action='{!! url('accounting') !!}' enctype="multipart/form-data" id="service-transaction-form">
    {{ csrf_field() }}
    <input type='hidden' name='_token' value='{{ Session::token() }}'>

    @if (count($errors) > 0)
        <div class="mb-4 rounded border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-800">
            <ul class="list-disc pl-5 space-y-1 m-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4 flex items-center gap-2">
            <x-ui.icon name="receipt" size="sm" class="text-slate-400" />
            <h2 class="text-base font-semibold text-slate-900">Billing Detail</h2>
        </div>
        <div class="px-5 py-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 max-w-3xl">
                <div>
                    <label for="office_id" class="{{ $labelClass }}">{{ trans('Office') }}</label>
                    <select name="office_id" id="office_id" class="{{ $inputClass }}">
                        @foreach ($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $labelClass }}">{{ trans('Tour') }}</label>
                    <input class="{{ $inputClass }} bg-slate-50 text-slate-500" name='tours' value='{{ $tourName }}' disabled>
                </div>

                <div id="services" style="display:none">
                    <label for="service" class="{{ $labelClass }}">{{ trans('Service') }}</label>
                    <select id="service" name="service" class="{{ $inputClass }}"></select>
                </div>
                <div id="service_div"></div>

                <div>
                    <label for="total_amount" class="{{ $labelClass }}">{!! trans('Total Amount') !!} <span class="text-danger-600">*</span></label>
                    <input type="text" name="total_amount" id="total_amount" class="{{ $inputClass }}" value="">
                </div>
                <div>
                    <label for="extra_amount" class="{{ $labelClass }}">{!! trans('Extra Amount') !!} <span class="text-danger-600">*</span></label>
                    <input type="text" name="extra_amount" id="extra_amount" class="{{ $inputClass }}" value="">
                </div>
                <div>
                    <label for="amount_payable" class="{{ $labelClass }}">{!! trans('Amount Payable*') !!} <span class="text-danger-600">*</span></label>
                    <input type="text" name="amount_payable" id="amount_payable" class="{{ $inputClass }}" value="">
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 flex items-center justify-end gap-2 border-t border-slate-200 pt-4">
        <x-ui.button as="a" href="javascript:history.back()" variant="secondary" icon="arrow-left" class="back_btn">{!! trans('main.Back') !!}</x-ui.button>
        <x-ui.button type="submit" icon="check">{!! trans('main.Save') !!}</x-ui.button>
    </div>
</form>
@endsection

@push('scripts')
    <script type="text/javascript" src='{{ asset('js/rooms.js') }}'></script>
    <script type="text/javascript" src='{{ asset('js/hide_elements.js') }}'></script>

    <script type="text/javascript">
        function readURL(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#pic').attr('src', e.target.result);
                    $('#file-caption-name').html(input.files[0].name);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInp").change(function() {
            readURL(this);
        });


		  APP_URL = '{{url("/supplierdropdown")}}';
        $(document).ready(function() {

			$("#tour_id").change(function() {
                var selectedValue = $(this).val();

                $.ajax({
            type: "GET",
            url: APP_URL + '/' + selectedValue,


            success: function(result) {
               console.log(result);
				if(result[0] === ""){
					 $("#service_div").show();
					$("#services").hide();
                $("#service_div").html(`<h3> Please Add Service in tour </h3>`);
               }
               else{
				   $("#services").show();
				   $("#service_div").hide();
                $("#service").html(result);

               }
            },
            error: function(result) {
                console.log(result);
            }
        });
                });
            $("#serviceDropdown").change(function() {



            });
        });
    </script>
@endpush
