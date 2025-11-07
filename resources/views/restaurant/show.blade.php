@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Restaurant Details')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('restaurant.index') }}">Restaurants</a></li>
                            <li class="breadcrumb-item active">{{ $restaurant->name }}</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title"><i class="ti ti-tools-kitchen-2 me-2"></i>{{ $restaurant->name }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('restaurant.index') }}" class="btn btn-ghost-secondary"><i class="ti ti-arrow-left me-1"></i>Back</a>
                    @if (Auth::user()->can('restaurant.edit'))
                        <a href="{{ route('restaurant.edit', $restaurant->id) }}" class="btn btn-warning"><i class="ti ti-edit me-1"></i>Edit</a>
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
                <li class="nav-item"><a href="#menu-tab" class="nav-link" data-bs-toggle="tab"><i class="ti ti-list me-1"></i>Menu</a></li>
                <li class="nav-item"><a href="#invoices-tab" class="nav-link" data-bs-toggle="tab" id="invoices_tab"><i class="ti ti-file-invoice me-1"></i>Invoices</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="info-tab">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td class="w-50"><strong>Name:</strong></td><td>{{ $restaurant->name ?? '—' }}</td></tr>
                                <tr><td><strong>Address 1:</strong></td><td>{{ $restaurant->address_first ?? '—' }}</td></tr>
                                <tr><td><strong>Address 2:</strong></td><td>{{ $restaurant->address_second ?? '—' }}</td></tr>
                                <tr><td><strong>Country:</strong></td><td>{{ !empty($restaurant->country) ? \App\Helper\CitiesHelper::getCountryById($restaurant->country)['name'] ?? '—' : '—' }}</td></tr>
                                <tr><td><strong>City:</strong></td><td>{{ !empty($restaurant->city) ? \App\Helper\CitiesHelper::getCityById($restaurant->city)['name'] ?? '—' : '—' }}</td></tr>
                                <tr><td><strong>Code:</strong></td><td>{{ $restaurant->code ?? '—' }}</td></tr>
                                <tr><td><strong>Work Phone:</strong></td><td>{{ $restaurant->work_phone ?? '—' }}</td></tr>
                                <tr><td><strong>Work Fax:</strong></td><td>{{ $restaurant->work_fax ?? '—' }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td class="w-50"><strong>Work Email:</strong></td><td>{{ $restaurant->work_email ?? '—' }}</td></tr>
                                <tr><td><strong>Contact Name:</strong></td><td>{{ $restaurant->contact_name ?? '—' }}</td></tr>
                                <tr><td><strong>Contact Phone:</strong></td><td>{{ $restaurant->contact_phone ?? '—' }}</td></tr>
                                <tr><td><strong>Contact Email:</strong></td><td>{{ $restaurant->contact_email ?? '—' }}</td></tr>
                                <tr><td><strong>Comments:</strong></td><td>{{ $restaurant->comments ?? '—' }}</td></tr>
                                <tr><td><strong>Internal Comments:</strong></td><td>{{ $restaurant->int_comments ?? '—' }}</td></tr>
                                <tr><td><strong>Website:</strong></td><td>{{ $restaurant->website ?? '—' }}</td></tr>
                                <tr><td><strong>Rate:</strong></td><td>{{ $restaurant->rate_name ?? '—' }}</td></tr>
                                <tr><td><strong>Criterias:</strong></td><td>
                                    @forelse($criterias as $criteria)
                                        @foreach($restaurant->criterias as $item)
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
                                <input type="hidden" name="reference_id" value="{{ $restaurant->id }}">
                                <input type="hidden" name="reference_type" value="{{ \App\Comment::$services['restaurant'] ?? 'restaurant' }}">
                                <button type="submit" class="btn btn-primary"><i class="ti ti-send me-1"></i>Send</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="history-tab"><div id="history-container"></div></div>
                <div class="tab-pane fade" id="menu-tab"><div id="menu-container"></div></div>
                <div class="tab-pane fade" id="invoices-tab"><div id="invoices-container"></div></div>
            </div>
        </div>
    </div>
</div>
<span id="services_name" data-service-name='Restaurant' data-history-route="{{ route('services_history', ['id' => $restaurant->id]) }}" hidden></span>
@endsection

@section('post_scripts')
<script src="{{ asset('js/comment.js') }}"></script>
@endsection
