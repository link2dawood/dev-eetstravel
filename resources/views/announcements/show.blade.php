@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $announcement->title }}</h3>
                    <div class="card-tools pull-right">
                        <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                        @can('update', $announcement)
                            <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    @include('announcements.show_main', ['announcement' => $announcement])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
