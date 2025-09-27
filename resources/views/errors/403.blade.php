@extends('scaffold-interface.layouts.app')
@section('title', 'Access Denied')
@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-lock"></i>
                        403 - Access Denied
                    </h3>
                </div>
                <div class="box-body text-center">
                    <div style="font-size: 120px; color: #f39c12; margin: 20px 0;">
                        <i class="fa fa-lock"></i>
                    </div>

                    <h2>Access Denied</h2>

                    @if(isset($message))
                        <p class="lead">{{ $message }}</p>
                    @else
                        <p class="lead">You don't have permission to access this resource.</p>
                    @endif

                    <p>If you believe this is an error, please contact your administrator.</p>

                    <div class="row" style="margin-top: 30px;">
                        <div class="col-sm-6">
                            <a href="{{ url('/home') }}" class="btn btn-primary btn-lg">
                                <i class="fa fa-dashboard"></i>
                                Go to Dashboard
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="javascript:history.back()" class="btn btn-default btn-lg">
                                <i class="fa fa-arrow-left"></i>
                                Go Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection