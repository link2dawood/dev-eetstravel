@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Hotel Details')

@section('content')
<div class="container-xl">
    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hotel.index') }}">Hotels</a></li>
                            <li class="breadcrumb-item active">{{ $hotel->name }}</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-building me-2"></i>{{ $hotel->name }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('hotel.index') }}" class="btn btn-ghost-secondary">
                        <i class="ti ti-arrow-left me-1"></i>Back
                    </a>
                    @if (Auth::user()->can('hotel.edit'))
                        <a href="{{ route('hotel.edit', $hotel->id) }}" class="btn btn-warning">
                            <i class="ti ti-edit me-1"></i>Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                <li class="nav-item"><a href="#info-tab" class="nav-link active" data-bs-toggle="tab"><i class="ti ti-info-circle me-1"></i>Info</a></li>
                <li class="nav-item"><a href="#contacts-tab" class="nav-link" data-bs-toggle="tab"><i class="ti ti-users me-1"></i>Contacts</a></li>
                <li class="nav-item"><a href="#history-tab" class="nav-link" data-bs-toggle="tab"><i class="ti ti-history me-1"></i>History</a></li>
                <li class="nav-item"><a href="#agreement-tab" class="nav-link" data-bs-toggle="tab" id="agreement_tab"><i class="ti ti-file-certificate me-1"></i>Agreements</a></li>
                <li class="nav-item"><a href="#kontingent-tab" class="nav-link" data-bs-toggle="tab" id="kontingent_tab"><i class="ti ti-calendar-stats me-1"></i>Allotment</a></li>
                <li class="nav-item"><a href="#menu-tab" class="nav-link" data-bs-toggle="tab"><i class="ti ti-list me-1"></i>Menu</a></li>
                <li class="nav-item"><a href="#season-tab" class="nav-link" data-bs-toggle="tab" id="season_tab"><i class="ti ti-sun me-1"></i>Season Prices</a></li>
                <li class="nav-item"><a href="#invoices-tab" class="nav-link" data-bs-toggle="tab" id="invoices_tab"><i class="ti ti-file-invoice me-1"></i>Invoices</a></li>
                    </ul>
                </div>
        <div class="card-body">
                <div class="tab-content">
                {{-- Info Tab --}}
                <div class="tab-pane fade show active" id="info-tab" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="mb-3">Hotel Information</h3>
                            <table class="table table-borderless">
                                <tr><td class="w-50"><strong>Name:</strong></td><td>{{ $hotel->name ?? '—' }}</td></tr>
                                <tr><td><strong>Address 1:</strong></td><td>{{ $hotel->address_first ?? '—' }}</td></tr>
                                <tr><td><strong>Address 2:</strong></td><td>{{ $hotel->address_second ?? '—' }}</td></tr>
                                <tr><td><strong>Code:</strong></td><td>{{ $hotel->code ?? '—' }}</td></tr>
                                <tr><td><strong>Country:</strong></td><td>{{ !empty($hotel->country) ? \App\Helper\CitiesHelper::getCountryById($hotel->country)['name'] ?? '—' : '—' }}</td></tr>
                                <tr><td><strong>City:</strong></td><td>{{ !empty($hotel->city) ? \App\Helper\CitiesHelper::getCityById($hotel->city)['name'] ?? '—' : '—' }}</td></tr>
                                <tr><td><strong>Work Phone:</strong></td><td>{{ $hotel->work_phone ?? '—' }}</td></tr>
                                <tr><td><strong>Work Fax:</strong></td><td>{{ $hotel->work_fax ?? '—' }}</td></tr>
                                <tr><td><strong>Work Email:</strong></td><td>{{ $hotel->work_email ?? '—' }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h3 class="mb-3">Contact & Details</h3>
                            <table class="table table-borderless">
                                <tr><td class="w-50"><strong>Contact Name:</strong></td><td>{{ $hotel->contact_name ?? '—' }}</td></tr>
                                <tr><td><strong>Contact Phone:</strong></td><td>{{ $hotel->contact_phone ?? '—' }}</td></tr>
                                <tr><td><strong>Contact Email:</strong></td><td>{{ $hotel->contact_email ?? '—' }}</td></tr>
                                <tr><td><strong>Comments:</strong></td><td>{{ $hotel->comments ?? '—' }}</td></tr>
                                <tr><td><strong>Internal Comments:</strong></td><td>{{ $hotel->int_comments ?? '—' }}</td></tr>
                                <tr><td><strong>Rate:</strong></td><td>{{ $hotel->rate_name ?? '—' }}</td></tr>
                                <tr><td><strong>Website:</strong></td><td>
                                    @if($hotel->website)
                                        <a href="{{ $hotel->website }}" target="_blank">{{ $hotel->website }}</a>
                                    @else — @endif
                                </td></tr>
                                <tr><td><strong>Criterias:</strong></td><td>
                                    @forelse($criterias as $criteria)
                                        @foreach($hotel->criterias as $item)
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
                    
                    {{-- Comments Section --}}
                    <div class="card mt-4">
                        <div class="card-header"><h3 class="card-title"><i class="ti ti-message me-2"></i>Comments</h3></div>
                        <div class="card-body"><div id="show_comments" style="max-height: 400px; overflow-y: auto;"></div></div>
                        <div class="card-footer">
                            <form method="POST" action="{{ route('comment.store') }}" enctype="multipart/form-data" id="form_comment">
                                @csrf
                                <div class="mb-3">
                                    <span id="author_name" class="text-muted" style="display:none;">
                                        <span id="name"></span><a href="#" id="reply_close" class="ms-2"><i class="ti ti-x"></i></a>
                                    </span>
                                    <textarea class="form-control" id="content" name="content" rows="3" placeholder="Ctrl + Enter to post comment"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Files</label>
                                        @component('component.file_upload_field')@endcomponent
                                    </div>
                                <input type="hidden" id="parent_comment" name="parent">
                                <input type="hidden" id="default_reference_id" name="reference_id" value="{{ $hotel->id }}">
                                <input type="hidden" id="default_reference_type" name="reference_type" value="{{ \App\Comment::$services['hotel'] ?? 'hotel' }}">
                                <button type="submit" class="btn btn-primary" id="btn_send_comment">
                                    <i class="ti ti-send me-1"></i>Send
                                </button>
                                </form>
                        </div>
                    </div>
                </div>

                {{-- Contacts Tab --}}
                <div class="tab-pane fade" id="contacts-tab" role="tabpanel">
                            @if($contacts->count())
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Full Name</th>
                                        <th>Mobile Phone</th>
                                        <th>Work Phone</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contacts as $contact)
                                        <tr>
                                            <td>{{ $contact->full_name }}</td>
                                            <td>{{ $contact->mobile_phone }}</td>
                                            <td>{{ $contact->work_phone }}</td>
                                            <td>{{ $contact->email }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty py-5">
                            <p class="empty-title">No contacts found</p>
                            <p class="empty-subtitle text-muted">This hotel doesn't have any contacts yet</p>
                        </div>
                                        @endif
                    </div>

                {{-- History Tab --}}
                <div class="tab-pane fade" id="history-tab" role="tabpanel">
                    <div id="history-container"></div>
                </div>

                {{-- Agreements Tab --}}
                <div class="tab-pane fade" id="agreement-tab" role="tabpanel">
                    <div class="mb-3">
                        @if(Auth::user()->can('create_agreements'))
                            <a href="{{ route('create_agreements', ['id' => $hotel->id]) }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>Add Agreement
                            </a>
                        @endif
                    </div>
                    <div id="agreements-container"></div>
                </div>

                {{-- Kontingent/Allotment Tab --}}
                <div class="tab-pane fade" id="kontingent-tab" role="tabpanel">
                    <div id="kontingent-container"></div>
                </div>

                {{-- Menu Tab --}}
                <div class="tab-pane fade" id="menu-tab" role="tabpanel">
                    <div id="menu-container"></div>
                </div>

                {{-- Season Prices Tab --}}
                <div class="tab-pane fade" id="season-tab" role="tabpanel">
                    <div id="season-container"></div>
                </div>

                {{-- Invoices Tab --}}
                <div class="tab-pane fade" id="invoices-tab" role="tabpanel">
                    <div id="invoices-container"></div>
                </div>
            </div>
        </div>
    </div>
    </div>

<span id="showPreviewBlock" data-info="true" hidden></span>
<span id="services_name" data-service-name='Hotel' data-history-route="{{ route('services_history', ['id' => $hotel->id]) }}" hidden></span>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
<script src="{{ asset('js/agreement_rooms.js') }}"></script>
<script src="{{ asset('js/roomlist.js') }}"></script>
<script src="{{ asset('js/seasons_rooms.js') }}"></script>
<script src="{{ asset('js/rooms.js') }}"></script>
@endsection
