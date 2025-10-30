@extends('scaffold-interface.layouts.tabler-app')
@section('title','Hotels')
@section('content')
<style>
.action-buttons {
    display: flex;
    gap: 8px;
    align-items: center;
    justify-content: center;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 4px;
}

.action-btn svg {
    width: 20px;
    height: 20px;
}

.action-btn.show svg {
    stroke: #3b82f6;
}

.action-btn.edit svg {
    stroke: #f59e0b;
}

.action-btn.delete svg {
    stroke: #ef4444;
}

.action-btn:hover {
    transform: scale(1.15);
}

.action-btn.show:hover {
    background-color: rgba(59, 130, 246, 0.1);
}

.action-btn.edit:hover {
    background-color: rgba(245, 158, 11, 0.1);
}

.action-btn.delete:hover {
    background-color: rgba(239, 68, 68, 0.1);
}
</style>

    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="ti ti-home"></i> Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Hotels</li>
                            </ol>
                        </nav>
                    </div>
                    <h2 class="page-title">
                        Hotels
                    </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('hotel.create'), \App\Hotel::class) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        @if(session('export_all'))
                        <div class="alert alert-info m-3">
                            {{session('export_all')}}
                        </div>
                        @endif

                        <div class="card-header">
                            <h3 class="card-title">Hotels List</h3>
                            <div class="col-auto ms-auto">
                                <button class="btn btn-success btn-sm" onclick="exportTableToCSV('hotels-table', 'hotels_export.csv')">
                                    <i class="ti ti-download"></i> Export CSV
                                </button>
                            </div>
                        </div>

                        <div class="card-body border-bottom py-3">
                            <div class="d-flex">
                                <div class="text-muted">
                                    Show
                                    <div class="mx-2 d-inline-block">
                                        <input type="text" class="form-control form-control-sm" value="10" size="3" aria-label="Items per page">
                                    </div>
                                    entries
                                </div>
                                <div class="ms-auto text-muted">
                                    Search:
                                    <div class="ms-2 d-inline-block">
                                        <input type="text" id="hotels-search" class="form-control form-control-sm" placeholder="Search hotels..." onkeyup="filterTable('hotels-table', this.value)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="hotels-table" class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th class="w-1" onclick="sortTable(0, 'hotels-table')">Id <i class="ti ti-chevron-up"></i></th>
                                        <th onclick="sortTable(1, 'hotels-table')">{!!trans('main.Name')!!} <i class="ti ti-chevron-up"></i></th>
                                        <th onclick="sortTable(2, 'hotels-table')">{!!trans('main.Address')!!} <i class="ti ti-chevron-up"></i></th>
                                        <th onclick="sortTable(3, 'hotels-table')">{!!trans('main.Country')!!} <i class="ti ti-chevron-up"></i></th>
                                        <th onclick="sortTable(4, 'hotels-table')">{!!trans('main.City')!!} <i class="ti ti-chevron-up"></i></th>
                                        <th onclick="sortTable(5, 'hotels-table')">{!!trans('main.WorkPhone')!!} <i class="ti ti-chevron-up"></i></th>
                                        <th onclick="sortTable(6, 'hotels-table')">{!!trans('main.ContactEmail')!!} <i class="ti ti-chevron-up"></i></th>
                                        <th class="w-1">{!!trans('main.Actions')!!}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($hotels as $hotel)
                                    <tr>
                                        <td><span class="text-muted">{{ $hotel->id }}</span></td>
                                        <td>{{ $hotel->name }}</td>
                                        <td>{{ $hotel->address }}</td>
                                        <td>{{ $hotel->country_name ?? '' }}</td>
                                        <td>{{ $hotel->city_name ?? '' }}</td>
                                        <td>{{ $hotel->work_phone }}</td>
                                        <td>{{ $hotel->contact_email }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('hotel.show', $hotel->id) }}" class="action-btn show" title="View">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <circle cx="12" cy="12" r="2" />
                                                        <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('hotel.edit', $hotel->id) }}" class="action-btn edit" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                        <path d="M16 5l3 3" />
                                                    </svg>
                                                </a>
                                                <a href="#" onclick="confirmHotelDelete(event, '{{ route('hotel.destroy', $hotel->id) }}')" class="action-btn delete" title="Delete">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 7l16 0" />
                                                        <path d="M10 11l0 6" />
                                                        <path d="M14 11l0 6" />
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                        <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="ti ti-building icon mb-2" style="font-size: 3rem;"></i>
                                            <p>No hotels found</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer d-flex align-items-center">
                            {{ $hotels->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('hotels-table');
    });

    function confirmHotelDelete(event, deleteUrl) {
        event.preventDefault();
        event.stopPropagation();
        
        if (confirm("Are you sure you want to delete this hotel?")) {
            const form = document.createElement('form');
            form.action = deleteUrl;
            form.method = 'POST';
            form.style.display = 'none';

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush