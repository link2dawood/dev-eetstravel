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
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-bordered">
                            <thead>
                                <tr>
                                    <th>{!!trans('main.StartDate')!!}</th>
                                    <th>{!!trans('main.EndDate')!!}</th>
                                    <th>{!!trans('main.Name')!!}</th>
                                    <th>{!!trans('main.Rooms')!!}</th>
                                    <th>{!!trans('main.Description')!!}</th>
                                    <th class="text-end">{!!trans('main.Actions')!!}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hotel->agreements as $agreement)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($agreement->start_date)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($agreement->end_date)->format('d-m-Y') }}</td>
                                        <td>{{ $agreement->name }}</td>
                                        <td>
                                            @foreach($agreement->agreements_room_types as $item)
                                                <div>{{ $item->count }} &times; {{ optional($agreement->getRoom($item->room_type_id))->name }}</div>
                                            @endforeach
                                        </td>
                                        <td class="text-wrap" style="max-width: 32rem;">{{ $agreement->description }}</td>
                                        <td class="text-end text-nowrap">
                                            @if(Auth::user()->can('edit_agreements'))
                                                <a href="{{ route('edit_agreements', ['id' => $agreement->id, 'hotel_id' => $hotel->id]) }}" class="btn btn-primary btn-sm btn-icon" title="Edit"><i class="ti ti-edit"></i></a>
                                            @endif
                                            @if(Auth::user()->can('delete_agreements'))
                                                <button type="button" onclick="checkAgreement({{ $agreement->id }}, {{ $hotel->id }});" class="btn btn-danger btn-sm btn-icon" title="Delete"><i class="ti ti-trash"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No agreements for this hotel yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Kontingent/Allotment Tab: monthly room quota calendar (agreements vs. tour bookings) --}}
                <div class="tab-pane fade" id="kontingent-tab" role="tabpanel">
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-label" for="start_date">{!!trans('main.StartMonth')!!}</label>
                            <div class="input-group">
                                <span class="input-group-text date_calendar" role="button"><i class="ti ti-calendar"></i></span>
                                <input type="text" value="" name="start_date" id="start_date" class="form-control" autocomplete="off">
                                <button type="button" class="btn btn-primary" id="showMonth">{!!trans('main.Show')!!}</button>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-8 text-muted small">
                            Four months from the chosen month: contracted rooms, rooms used by tours, reserved allotment and the quota still available.
                        </div>
                    </div>
                    <div class="table-responsive kontingent-calendars">
                        <table class="calendar" id="calendar1"><thead id="calendar_head1"></thead></table>
                        <table class="calendar" id="calendar2"><thead id="calendar_head2"></thead></table>
                        <table class="calendar" id="calendar3"><thead id="calendar_head3"></thead></table>
                        <table class="calendar" id="calendar4"><thead id="calendar_head4"></thead></table>
                    </div>
                </div>

                {{-- Menu Tab --}}
                <div class="tab-pane fade" id="menu-tab" role="tabpanel">
                    <div class="mb-3">
                        @if(Auth::user()->can('menu.create'))
                            <a href="{{ route('menu.create', ['type' => 'hotel', 'id' => $hotel->id]) }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>{!!trans('main.AddMenu')!!}
                            </a>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-bordered">
                            <thead>
                                <tr>
                                    <th>{!!trans('main.Name')!!}</th>
                                    <th>{!!trans('main.Price')!!}</th>
                                    <th>{!!trans('main.Description')!!}</th>
                                    <th class="text-end">{!!trans('main.Actions')!!}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hotel->menus as $menu)
                                    <tr>
                                        <td><a href="{{ route('menu.show', ['menu' => $menu->id]) }}">{{ $menu->name }}</a></td>
                                        <td>{{ $menu->price }}</td>
                                        <td>{!! $menu->safeDescription() !!}</td>
                                        <td class="text-end text-nowrap">
                                            @if(Auth::user()->can('menu.edit'))
                                                <a href="{{ route('menu.edit', ['menu' => $menu->id]) }}" class="btn btn-primary btn-sm btn-icon" title="Edit"><i class="ti ti-edit"></i></a>
                                            @endif
                                            @if(Auth::user()->can('menu.destroy_menu'))
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#myModal" data-toggle="modal" data-target="#myModal" class="btn btn-danger btn-sm btn-icon delete" data-link="{{ route('menu.delete', ['id' => $menu->id], false) }}" title="Delete"><i class="ti ti-trash"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">No menus for this hotel yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Season Prices Tab --}}
                <div class="tab-pane fade" id="season-tab" role="tabpanel">
                    <div class="mb-3">
                        @if(Auth::user()->can('create_season'))
                            <a href="{{ route('create_season', ['id' => $hotel->id]) }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>{!!trans('main.AddSeason')!!}
                            </a>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-bordered">
                            <thead>
                                <tr>
                                    <th>{!!trans('main.StartDate')!!}</th>
                                    <th>{!!trans('main.EndDate')!!}</th>
                                    <th>{!!trans('main.Name')!!}</th>
                                    <th>{!!trans('main.Type')!!}</th>
                                    <th>{!!trans('main.Prices')!!}</th>
                                    <th>{!!trans('main.Description')!!}</th>
                                    <th class="text-end">{!!trans('main.Actions')!!}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hotel->seasons as $season)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($season->start_date)->format('d.m') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($season->end_date)->format('d.m') }}</td>
                                        <td>{{ $season->name }}</td>
                                        <td>{{ optional($season->getType($season->type))->name }}</td>
                                        <td>
                                            @foreach($season->seasons_room_types as $item)
                                                <div>{{ optional($season->getRoom($item->room_type_id))->name }} &ndash; {{ $item->price }}</div>
                                            @endforeach
                                        </td>
                                        <td class="text-wrap" style="max-width: 32rem;">{{ $season->description }}</td>
                                        <td class="text-end text-nowrap">
                                            @if(Auth::user()->can('edit_season'))
                                                <a href="{{ route('edit_season', ['id' => $season->id, 'hotel_id' => $hotel->id]) }}" class="btn btn-primary btn-sm btn-icon" title="Edit"><i class="ti ti-edit"></i></a>
                                            @endif
                                            @if(Auth::user()->can('delete_season'))
                                                <button type="button" onclick="checkAgreement({{ $season->id }}, {{ $hotel->id }}, 1);" class="btn btn-danger btn-sm btn-icon" title="Delete"><i class="ti ti-trash"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">No season prices for this hotel yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Invoices Tab --}}
                <div class="tab-pane fade" id="invoices-tab" role="tabpanel">
                    @include('component.supplier_invoices', ['invoices' => $invoices ?? collect(), 'tableId' => 'hotel-invoices-table'])
                </div>
            </div>
        </div>
    </div>
    </div>

<span id="showPreviewBlock" data-info="true" hidden></span>
<span id="services_name" data-service-name='Hotel' data-history-route="{{ route('services_history', ['id' => $hotel->id]) }}" hidden></span>

{{-- Allotment day detail: tours using the quota that day (filled by kontingentModal) --}}
<div id="modalCreateKontingent" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalCreateKontigentLabel">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalCreateKontigentLabel" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <input type="hidden" id="modalCreateKontigentQuota" value="">
                <input type="hidden" id="modalCreateKontigentValue" value="">
            </div>
            <div class="modal-body">
                <input type='hidden' name='_token' value='{{ Session::token() }}'>
                <input type='hidden' name='modal_create_tour' value="1">
                @foreach(['from_div', 'to_div'] as $listId)
                    <div class="table-responsive mb-3" style="max-height: 250px;">
                        <table class="table table-striped table-bordered table-hover package-service-table">
                            <thead>
                                <tr>
                                    <th>{!!trans('main.Tour')!!}</th>
                                    <th>{!!trans('main.FromTime')!!}</th>
                                    <th>{!!trans('main.Name')!!}</th>
                                    <th>{!!trans('main.Status')!!}</th>
                                    <th>{!!trans('main.Paid')!!}</th>
                                    <th>{!!trans('main.Rooms')!!}</th>
                                    <th>Pax</th>
                                    <th>{!!trans('main.Address')!!}</th>
                                    <th>{!!trans('main.Email')!!}</th>
                                    <th>{!!trans('main.Phone')!!}</th>
                                    <th>{!!trans('main.Description')!!}</th>
                                    <th>{!!trans('main.Actions')!!}</th>
                                </tr>
                            </thead>
                            <tbody class="ui-sortable" id="{{ $listId }}"></tbody>
                        </table>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{!!trans('main.Close')!!}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="warnReplace" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{!!trans('main.Warning')!!}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">{!!trans('main.Themaximumnumberofroomsavailable')!!}</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteAgreement" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{!!trans('main.Warning')!!}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">{!!trans('main.WouldyouliketoremoveThis')!!}?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{!!trans('main.Close')!!}</button>
                <button type="button" class="destroy btn btn-danger" onclick="deleteAgreement();">{!!trans('main.Agree')!!}</button>
                <input type="hidden" id="id_field" value="">
                <input type="hidden" id="hotel_id_field" value="">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="replacePackage" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{!!trans('main.Warning')!!}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">{!!trans('main.WouldyouliketoreplaceThis')!!}?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{!!trans('main.Close')!!}</button>
                <button type="button" class="destroy btn btn-primary" onclick="addFromAgreement();">{!!trans('main.Agree')!!}</button>
                <input type="hidden" id="id_package" value="0">
            </div>
        </div>
    </div>
</div>

<style>
    .kontingent-calendars .calendar { table-layout: fixed; border-collapse: collapse; margin-bottom: 1rem; }
    .kontingent-calendars .calendar td { white-space: nowrap; }
    .datepicker { z-index: 1151 !important; }
</style>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
<script src="{{ asset('js/agreement_rooms.js') }}"></script>
<script src="{{ asset('js/roomlist.js') }}"></script>
<script src="{{ asset('js/seasons_rooms.js') }}"></script>
<script src="{{ asset('js/rooms.js') }}"></script>
<script>
    // Allotment (kontingent) calendar, agreement/season delete and the allotment day modal.
    var hotelId = {{ (int) $hotel->id }};
    var removed = [];
    var show = true;
    var current_date = '';
    var monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    function dateDifference(d1, d2) {
        var fd = new Date(d1.getFullYear(), d1.getMonth(), d1.getDate());
        var sd = new Date(d2.getFullYear(), d2.getMonth(), d2.getDate());
        return Math.ceil((sd - fd) / 86400000);
    }

    function setMonths() {
        if (!show) return;
        var date = $('#start_date').datepicker('getDate') || new Date();
        show = false;
        for (var i = 1; i <= 4; i++) $('#calendar_head' + i).html('');
        genMonths(new Date(date.getFullYear(), date.getMonth(), 1), 1);
    }

    function genRow(text, startDate, endDate, arr, mode, line, month) {
        var days = dateDifference(startDate, endDate) + 1;
        if (days > 31) days--;
        var label = mode ? '<b>' + text + '</b>' : text;
        var back = mode ? '#dbdee1' : '#ffffff';
        var cells = '<td style="background-color:#dbdee1;border:1px solid #9ea5a8;text-align:center;min-width:220px;">' + label + '</td>';
        for (var ii = 0; ii < days; ii++) {
            var num = '', id = '', extra = '';
            if (mode) {
                num = ii + 1;
            } else if (arr && arr[ii]) {
                num = arr[ii];
                if (line == '1') {
                    extra = ' data-title="Tours at ' + (ii + 1) + ' ' + monthNames[startDate.getMonth()] + ' ' + startDate.getFullYear() + '" class="my-popover" data-pop="' + (ii + 1) + '"';
                }
                if (line == '4') id = 'quota_' + month + '_' + (ii + 1);
            }
            cells += '<td id="' + id + '"' + extra + ' style="background-color:' + back + ';border:1px solid #9ea5a8;text-align:center;min-width:32px;">' + num + '</td>';
        }
        return cells;
    }

    function genMonths(startDate, num) {
        var endDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 0);
        var days = dateDifference(startDate, endDate) + 1;
        $.ajax({
            type: 'GET',
            url: '{{ route('kontingent_agreements') }}',
            data: { hotel_id: hotelId, days: days, startDate: moment(startDate).format('Y-M-D'), endDate: moment(endDate).format('Y-M-D'), num: num },
            success: function (data) {
                var head = $('#calendar_head' + num);
                head.append('<tr>' + genRow(monthNames[startDate.getMonth()] + ' ' + startDate.getFullYear(), startDate, endDate, data, true, 0) + '</tr>');
                head.append('<tr>' + genRow('Contractual number of rooms', startDate, endDate, data[0], null, 1) + '</tr>');
                head.append('<tr>' + genRow('Number of rooms already used', startDate, endDate, data[1], null, 2) + '</tr>');
                head.append('<tr>' + genRow('Allotment Reserved', startDate, endDate, data[2], null, 3) + '</tr>');
                head.append('<tr>' + genRow('Available quota', startDate, endDate, data[3], null, 4, moment(endDate).format('MM')) + '</tr>');
                head.append('<tr>' + genRow('Current booking status %', startDate, endDate, data[4], null, 5) + '</tr>');
                if (num < 4) {
                    genMonths(new Date(startDate.getFullYear(), startDate.getMonth() + 1, 1), num + 1);
                } else {
                    show = true;
                }
            },
            error: function () {
                show = true;
                if (typeof window.appToast === 'function') window.appToast('The allotment calendar could not be loaded.', 'error');
            }
        });
    }

    function kontingentModal(date, id, pop, hotel_name) {
        var content = $(document).find('#pop_' + pop + '_' + id).html() || '';
        current_date = $(document).find('#pop_' + pop + '_' + id).data('dates');
        content = content.replace(/div/g, 'tr').replace(/span/g, 'td');
        var modal = $('#modalCreateKontingent');
        modal.find('#modalCreateKontigentLabel').html(date);
        modal.find('#from_div').html(content);
        modal.find('#modalCreateKontigentQuota').val($(document).find('#quota_' + pop + '_' + id).text());
        modal.find('#modalCreateKontigentValue').val($(document).find('#pop_' + pop + '_' + id).closest('a').text().split('\n')[0]);
        modal.find('#from_div .ui-sortable-handle .add-service-button').parent().parent().addClass('hidden');
        modal.find('#from_div .ui-sortable-handle .delete-service-button').parent().parent().removeClass('hidden');
        modal.find('#to_div').html(content.replace(/agreement_from_/g, 'agreement_to_'));
        modal.find('#to_div .ui-sortable-handle .add-service-button').parent().parent().removeClass('hidden');
        modal.find('#to_div .ui-sortable-handle .delete-service-button').parent().parent().addClass('hidden');
        modal.modal('show');
    }

    function checkAgreement(id, hotel_id, season) {
        var modal = $('#deleteAgreement');
        modal.find('#id_field').val(id);
        modal.find('#hotel_id_field').val(hotel_id);
        modal.find('.btn-danger').attr('onclick', season ? 'deleteAgreement(1);' : 'deleteAgreement();');
        modal.modal('show');
    }

    function deleteAgreement(season) {
        var modal = $('#deleteAgreement');
        var id = modal.find('#id_field').val(), hotel_id = modal.find('#hotel_id_field').val();
        modal.modal('hide');
        location.href = (season ? '/season/' : '/agreements/') + hotel_id + '/delete/' + id;
    }

    function replaceFromPackages(id) {
        $('#modalCreateKontingent').modal('hide');
        $('#replacePackage').one('hidden.bs.modal', function () { $('#modalCreateKontingent').modal('show'); });
        $('#replacePackage').find('#id_package').val(id);
        $('#replacePackage').modal('show');
    }

    function deleteHotelFromPackages(package_id) {
        $('#modalCreateKontingent').modal('hide');
        var modal = $('#deleteAgreement');
        modal.find('.btn-danger').attr('onclick', null).off('click').one('click', function () {
            modal.modal('hide');
            $.ajax({
                type: 'POST',
                url: '{{ route('kontingent_delete') }}',
                data: { package_id: package_id },
                success: function () {
                    modal.find('.btn-danger').attr('onclick', 'deleteAgreement();');
                    removed = [];
                    setMonths();
                },
                error: function () {
                    if (typeof window.appToast === 'function') window.appToast('The booking could not be removed from the allotment.', 'error');
                }
            });
        });
        modal.modal('show');
    }

    function addFromAgreement() {
        var id = $('#replacePackage').find('#id_package').val(),
            tour = $('#modalCreateKontingent').find('#agreement_from_' + id + ' #tour_id').val(),
            quota = $('#modalCreateKontingent').find('#modalCreateKontigentQuota').val(),
            value = $('#modalCreateKontingent').find('#modalCreateKontigentValue').val();
        if (!quota) quota = value;
        removed.push([id, parseInt(tour), parseInt(quota)]);
        $('#replacePackage').off('hidden.bs.modal').modal('hide');
        $.ajax({
            type: 'GET',
            url: '{{ route('kontingent_save') }}',
            data: { replace_id: JSON.stringify(removed), hotel_id: hotelId, dates: current_date },
            success: function (data) {
                if (data['error']) {
                    $('#warnReplace').modal('show');
                } else {
                    $('#modalCreateKontingent').modal('hide');
                    setMonths();
                }
            },
            error: function () {
                if (typeof window.appToast === 'function') window.appToast('The allotment could not be saved.', 'error');
            }
        });
    }

    $('#warnReplace').on('hidden.bs.modal', function () { $('#modalCreateKontingent').modal('show'); });
    $('#modalCreateKontingent').on('hidden.bs.modal', function () { removed = []; });

    $(document).ready(function () {
        var today = new Date();
        $('#start_date').datepicker({ format: 'mm-yyyy', startView: 'months', minViewMode: 'months', autoclose: true });
        $('#start_date').datepicker('setDate', new Date(today.getFullYear(), today.getMonth(), 1));
        $('#showMonth').on('click', setMonths);
        $('.date_calendar').on('click', function () { $('#start_date').datepicker('show'); });
        // Load the calendar the first time the Allotment tab is opened
        $('a[href="#kontingent-tab"]').one('shown.bs.tab', setMonths);
    });
</script>
@endsection
