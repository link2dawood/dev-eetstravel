@extends('scaffold-interface.layouts.app')
@section('title', 'Create')
@section('content')
    @include('layouts.title', [
        'title' => 'Office Fees',
        'sub_title' => 'Edit Office',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Invoices', 'icon' => 'suitcase', 'route' => route('tour.index')],
            ['title' => 'Create', 'route' => null],
        ],
    ])
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
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
                <form method='POST'  action="{{route('office.update', ['office' => $offices->id])}}"enctype="multipart/form-data">
                    {{ csrf_field() }}
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
                    `<div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button type="button" class='btn btn-primary back_btn'>{!! trans('main.Back') !!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!! trans('main.Save') !!}</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <input type='hidden' name='_token' value='{{ Session::token() }}'>
                            <div class="form-group">
                                <label for="name">{!! trans('Office Name') !!} *</label>
                                {!! Form::text('office_name', $offices->office_name, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group">
                                <label for="name">{!! trans('Office Address') !!} *</label>
                                {!! Form::text('office_address', $offices->office_address, ['class' => 'form-control']) !!}
                            </div>
							<div class="form-group">
                                <label for="name">{!! trans('Bank Name') !!} *</label>
                                {!! Form::text('bank_name', $offices->bank_name, ['class' => 'form-control']) !!}
                            </div>
							<div class="form-group">
                                <label for="name">{!! trans('Account No') !!} *</label>
                                {!! Form::text('account_no', $offices->account_no, ['class' => 'form-control']) !!}
                            </div>
							<div class="form-group">
                                <label for="name">{!! trans('Swift Code') !!} *</label>
                                {!! Form::text('swift_code', $offices->swift_code, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group">
                                <label for="name">{!! trans('Tel') !!} *</label>
                                {!! Form::text('tel', $offices->tel, ['class' => 'form-control']) !!}
                            </div>
							<div class="form-group">
                                <label for="name">{!! trans('Fax') !!} *</label>
                                {!! Form::text('fax', $offices->fax, ['class' => 'form-control']) !!}
                            </div>
							

                        </div>
                    </div>

                </form>
            </div>
        </div>
    </section>
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

      
    </script>
@endsection
