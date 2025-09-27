@extends('scaffold-interface.layouts.app')
@section('title', 'Settings')
@section('content')
	@include('layouts.title',
        ['title' => 'Settings', 'sub_title' => 'Settings List', 'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Settings', 'icon' => null, 'route' => null]]])
	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
				
			</div>
		</div>
	</section>
@endsection