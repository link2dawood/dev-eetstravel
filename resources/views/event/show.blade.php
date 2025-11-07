@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Event Details')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('event.index') }}">Events</a></li>
                            <li class="breadcrumb-item active">{{ $event->name }}</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title"><i class="ti ti-calendar-event me-2"></i>{{ $event->name }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('event.index') }}" class="btn btn-ghost-secondary"><i class="ti ti-arrow-left me-1"></i>Back</a>
                    @if (Auth::user()->can('event.edit'))
                        <a href="{{ route('event.edit', $event->id) }}" class="btn btn-warning"><i class="ti ti-edit me-1"></i>Edit</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                <li class="nav-item"><a href="#info-tab" class="nav-link active" data-bs-toggle="tab"><i class="ti ti-info-circle me-1"></i>Info</a></li>
                <li class="nav-item"><a href="#history-tab" class="nav-link" data-bs-toggle="tab"><i class="ti ti-history me-1"></i>History</a></li>
                <li class="nav-item"><a href="#invoices-tab" class="nav-link" data-bs-toggle="tab" id="invoices_tab"><i class="ti ti-file-invoice me-1"></i>Invoices</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="info-tab">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td class="w-50"><strong>Name:</strong></td><td>{{ $event->name ?? '—' }}</td></tr>
                                <tr><td><strong>Address 1:</strong></td><td>{{ $event->address_first ?? '—' }}</td></tr>
                                <tr><td><strong>Address 2:</strong></td><td>{{ $event->address_second ?? '—' }}</td></tr>
                                <tr><td><strong>Country:</strong></td><td>{{ !empty($event->country) ? \App\Helper\CitiesHelper::getCountryById($event->country)['name'] ?? '—' : '—' }}</td></tr>
                                <tr><td><strong>City:</strong></td><td>{{ !empty($event->city) ? \App\Helper\CitiesHelper::getCityById($event->city)['name'] ?? '—' : '—' }}</td></tr>
                                <tr><td><strong>Code:</strong></td><td>{{ $event->code ?? '—' }}</td></tr>
                                <tr><td><strong>Work Phone:</strong></td><td>{{ $event->work_phone ?? '—' }}</td></tr>
                                <tr><td><strong>Work Fax:</strong></td><td>{{ $event->work_fax ?? '—' }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td class="w-50"><strong>Work Email:</strong></td><td>{{ $event->work_email ?? '—' }}</td></tr>
                                <tr><td><strong>Contact Name:</strong></td><td>{{ $event->contact_name ?? '—' }}</td></tr>
                                <tr><td><strong>Contact Phone:</strong></td><td>{{ $event->contact_phone ?? '—' }}</td></tr>
                                <tr><td><strong>Contact Email:</strong></td><td>{{ $event->contact_email ?? '—' }}</td></tr>
                                <tr><td><strong>Comments:</strong></td><td>{{ $event->comments ?? '—' }}</td></tr>
                                <tr><td><strong>Internal Comments:</strong></td><td>{{ $event->int_comments ?? '—' }}</td></tr>
                                <tr><td><strong>Website:</strong></td><td>{{ $event->website ?? '—' }}</td></tr>
                                <tr><td><strong>Rate:</strong></td><td>{{ $event->rate_name ?? '—' }}</td></tr>
                                <tr><td><strong>Criterias:</strong></td><td>
                                    @forelse($criterias as $criteria)
                                        @foreach($event->criterias as $item)
                                            @if($criteria->id == $item->criteria_id)
                                                <span class="badge bg-blue-lt me-1">{{ $criteria->name }}</span>
                                            @endif
                                        @endforeach
                                    @empty — @endforelse
                                </td></tr>
                            </table>
                        </div>
                    </div>
                    @component('component.files', ['files' => $files])@endcomponent
                    <div class="card mt-4">
                        <div class="card-header"><h3 class="card-title"><i class="ti ti-message me-2"></i>Comments</h3></div>
                        <div class="card-body"><div id="show_comments"></div></div>
                        <div class="card-footer">
                            <form method="POST" action="{{ route('comment.store') }}" enctype="multipart/form-data" id="form_comment">
                                @csrf
                                <textarea class="form-control mb-3" id="content" name="content" rows="3" placeholder="Ctrl + Enter to post comment"></textarea>
                                <input type="hidden" name="reference_id" value="{{ $event->id }}">
                                <input type="hidden" name="reference_type" value="{{ \App\Comment::$services['event'] ?? 'event' }}">
                                <button type="submit" class="btn btn-primary"><i class="ti ti-send me-1"></i>Send</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="history-tab"><div id="history-container"></div></div>
                <div class="tab-pane fade" id="invoices-tab"><div id="invoices-container"></div></div>
            </div>
        </div>
    </div>
</div>
<span id="services_name" data-service-name='Event' data-history-route="{{ route('services_history', ['id' => $event->id]) }}" hidden></span>
@endsection

@section('post_scripts')
<script src="{{ asset('js/comment.js') }}"></script>
@endsection
