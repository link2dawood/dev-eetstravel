@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')

<section class="content">
	 <div class="box box-primary">
            <div class="box box-body">
				<div class="col-md-5">
    <h1>
        {!!trans('main.ShowTourPackage')!!}
    </h1>
    <br>

    <br>
    <table class = 'table table-bordered'>
        <thead>
			<h1>Package Detail</h1>
        </thead>
        <tbody>
            <tr>
                <td>
                    <b><i>{!!trans('Name')!!}: </i></b>
                </td>
                <td>{!!$tour_package->name!!}</td>
            </tr>
            <tr>
                <td>
                    <b><i>{!!trans('Description')!!}: </i></b>
                </td>
                <td>{!!$tour_package->description!!}</td>
            </tr>
            <tr>
                <td>
                    <b><i>{!!trans('main.status')!!}: </i></b>

                <td>{!!$tour_package->status!!}</td>
            </tr>
        </tbody>
    </table>

    <span id="default_reference_id" data-info="{{ $tour_package->id }}"></span>
    <span id="default_reference_type" data-info="{{ \App\Comment::$services['tour_package'] }}"></span>
    <span id="default_token_val" data-info="{{ csrf_token() }}"></span>
    <div id="commentContainer">

    </div>
				</div>	
				
				<div class ="row">
					<h1>Hotel Offers</h1>
		 		</div>
		 </div>
	</div>
	
</section>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection