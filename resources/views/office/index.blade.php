@extends('scaffold-interface.layouts.app')
@section('content')
@include('layouts.title',
['title' => 'Office Fees', 'sub_title' => 'Office List',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Tours', 'icon' => 'suitcase', 'route' => null]]])

<section class="content">
    <div class="box box-primary">
        <div class="box-body">
			
            <div>
                <div id="tour_create">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('office.create'), \App\Tour::class) !!}
                </div>

            </div>
            @if(session('message_buses'))
            <div class="alert alert-info col-md-12" style="text-align: center;">
                {{session('message_buses')}}
            </div>
            @endif
         
            <br>
            <br>
      
			<div class="table-responsive">
            <table id="offices-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>

                <thead>
                    <tr>
                        <th>id</th>
                        <th>Office Name</th>
                        <th>Office Address</th>
						<th>Bank Name</th>
                        <th>Account No</th>
                        <th>Swift Code</th>
                        <th>Tel</th>
                        <th>Fax</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($officesData as $office)
                    <tr>
                        <td>{{ $office->id }}</td>
                        <td>{{ $office->office_name }}</td>
                        <td>{{ $office->office_address }}</td>
                        <td>{{ $office->bank_name }}</td>
                        <td>{{ $office->account_no }}</td>
                        <td>{{ $office->swift_code }}</td>
                        <td>{{ $office->tel }}</td>
                        <td>{{ $office->fax }}</td>
                        <td>{!! $office->action_buttons !!}</td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
        </div>
    </div>
</section>

@endsection
