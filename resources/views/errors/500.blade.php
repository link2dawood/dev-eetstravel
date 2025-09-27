@extends('scaffold-interface.layouts.app')
@section('title', 'Server Error')
@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-exclamation-circle"></i>
                        500 - Internal Server Error
                    </h3>
                </div>
                <div class="box-body text-center">
                    <div style="font-size: 120px; color: #dd4b39; margin: 20px 0;">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>

                    <h2>Something went wrong!</h2>
                    <p class="lead">We're experiencing some technical difficulties. Please try again later.</p>

                    @if(isset($message) && config('app.debug'))
                        <div class="alert alert-info">
                            <strong>Debug Info:</strong> {{ $message }}
                        </div>
                    @endif

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