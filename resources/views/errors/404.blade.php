@extends('scaffold-interface.layouts.app')
@section('title', 'Page Not Found')
@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-exclamation-triangle"></i>
                        404 - Page Not Found
                    </h3>
                </div>
                <div class="box-body text-center">
                    <div style="font-size: 120px; color: #dd4b39; margin: 20px 0;">
                        <i class="fa fa-search"></i>
                    </div>

                    <h2>Oops! Page Not Found</h2>

                    @if(isset($message))
                        <p class="lead">{{ $message }}</p>
                    @else
                        <p class="lead">The page you are looking for could not be found.</p>
                    @endif

                    <p>Here are some helpful links instead:</p>

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

                    <hr style="margin: 30px 0;">

                    <div class="row">
                        <div class="col-sm-4">
                            <a href="{{ route('tour.index') }}" class="btn btn-info">
                                <i class="fa fa-suitcase"></i>
                                Tours
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('clients.index') }}" class="btn btn-info">
                                <i class="fa fa-users"></i>
                                Clients
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('announcements.index') }}" class="btn btn-info">
                                <i class="fa fa-bullhorn"></i>
                                Announcements
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection