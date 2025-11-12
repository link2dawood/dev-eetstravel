{{--Tour Status Error--}}
<div class="modal fade" tabindex="-1" id="error_tour">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_confirmed_hotel">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title">{{ trans('main.Warning') }}!</h4>
                </div>
                <div class="modal-body">
                    <h3 class="error_tour_message"></h3>
                </div>
                <div class="modal-footer">
                    <div class="btn-send-confirmed_hotel">
                        <button type="reset" class="btn btn-success modal-close" data-dismiss="modal">Ok</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" tabindex="-1" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('main.ConfirmDelete') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ trans('main.AreYouSureDelete') }}</p>
                <p><strong id="deleteTourName"></strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('main.Cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{{ trans('main.Delete') }}</button>
            </div>
        </div>
    </div>
</div>

<!--  TOUR TABLE  -->
<div class="box box-primary">
    @if(Auth::user()->can('dashboard.latest_tours'))
        <div class="box-header">
            <h4>{{ trans('main.LatestTours') }}</h4>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div id="tours">
                <div v-if="loading">
                    <div class="box-body" style="height: 120px;">
                        <div class="loader"></div>
                    </div>
                </div>
                <div v-else>
                    <table class="table table-striped table-hover clickable-rows" style='background:#fff'>
                        <thead>
                        <th>ID</th>
                        <th>{{ trans('main.Name') }}</th>
                        <th>{{ trans('main.DepDate') }}</th>
                        <th>{{ trans('main.RetDate') }}</th>
                        <th>{{ trans('main.Pax') }}</th>
                        <th>{{ trans('main.Begin') }}</th>
                        <th>{{ trans('main.End') }}</th>
                        <th>G/A</th>
                        <th>{{ trans('main.Invoice') }}</th>
                        <th>{{ trans('main.Status') }}</th>
                        <th>{{ trans('main.ExternalName') }}</th>
                        <th style="width: 140px">{{ trans('main.Actions') }}</th>
                        </thead>
                        <tbody>
                        <tr v-for="tour in paginatedTours" :key="tour.id" @click="showTour(tour)" class="clickable-row">
                            <td>@{{tour.id}}</td>
                            <td>@{{tour.name}}</td>
                            <td>@{{tour.departure_date}}</td>
                            <td>@{{tour.retirement_date}}</td>
                            <td>@{{tour.pax}} @{{showPaxFree(tour)}}</td>
                            <td>@{{tour.country_begin}} - @{{tour.city_begin}}</td>
                            <td>@{{tour.country_end}} - @{{tour.city_end}}</td>
                            <td>@{{tour.ga}}</td>
                            <td>@{{tour.invoice}}</td>
                            <td class="{{ \App\Helper\PermissionHelper::checkPermission('tour.edit') ? 'touredit-status' : '' }}"
                                :data-name-status="tour.status_name" :data-status-link="tour.status_link">
                                @{{tour.status_name}}
                            </td>
                            <td>@{{tour.external_name}}</td>
                            <td @click.stop>
                                @if(isset($useComponent) && $useComponent)
                                    @include('components.action-buttons', ['model' => $tour, 'routePrefix' => 'tour'])
                                @else
                                    <div class="btn-list flex-nowrap">
                                        <!-- SHOW BUTTON -->
                                        <a v-if="show" 
                                           :href="'/tour/' + tour.id"
                                           class="btn btn-sm btn-warning" 
                                           title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>

                                        <!-- EDIT BUTTON -->
                                        <a v-if="edit" 
                                           :href="'/tour/' + tour.id + '/edit'"
                                           class="btn btn-sm btn-primary" 
                                           title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </a>

                                        <!-- DELETE BUTTON -->
                                        <button v-if="destroy"
                                                type="button"
                                                class="btn btn-sm btn-danger delete-tour-btn"
                                                :data-tour-id="tour.id"
                                                :data-tour-name="tour.name"
                                                title="Delete"
                                                @click="deleteTour(tour)">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <!-- Pagination Controls -->
                    <div class="pagination-wrapper" v-if="tours && tours.length > 0">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Button -->
                                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                    <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>

                                <!-- Page Numbers -->
                                <li class="page-item" 
                                    v-for="page in visiblePages" 
                                    :key="page"
                                    :class="{ active: currentPage === page }">
                                    <a class="page-link" href="#" @click.prevent="changePage(page)">@{{ page }}</a>
                                </li>

                                <!-- Next Button -->
                                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                    <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        
                        <!-- Pagination Info -->
                        <div class="pagination-info text-center text-muted">
                            Showing @{{ startRecord }} to @{{ endRecord }} of @{{ tours.length }} entries
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer clearfix">
                @if(Auth::user()->can('tour.create'))
                    <a href="{{route('tour.create')}}" class="btn btn-primary">
                        <i class="fa fa-plus fa-md" aria-hidden="true"></i> {{ trans('main.NewTour') }}
                    </a>
                @endif
                @if(Auth::user()->can('tour.index'))
                    <a href="{{route('tour.index')}}" class="btn btn-outline-secondary float-end">
                        {{ trans('main.ViewAllTours') }}
                    </a>
                @endif
            </div>
            @else
                <div class="box-header">
                    <h4>{{ trans('main.LatestTours') }}</h4>
                </div>
                <div class="box-body">
                    {{ trans('main.Youdonthavepermissions') }}
                </div>
            @endif
        </div>
</div>
<!--  END TOUR TABLE  -->

<script>
$(function () {
    // Vue Instance
    new Vue({
        el: '#tours',

        data: {
            tours: null,
            show: false,
            edit: false,
            destroy: false,
            loading: true,
            currentPage: 1,
            perPage: 10,
            tourToDelete: null
        },

        computed: {
            totalPages: function() {
                if (!this.tours) return 0;
                return Math.ceil(this.tours.length / this.perPage);
            },
            
            paginatedTours: function() {
                if (!this.tours) return [];
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                return this.tours.slice(start, end);
            },
            
            visiblePages: function() {
                const total = this.totalPages;
                const current = this.currentPage;
                const pages = [];
                
                if (total <= 7) {
                    for (let i = 1; i <= total; i++) {
                        pages.push(i);
                    }
                } else {
                    if (current <= 4) {
                        for (let i = 1; i <= 5; i++) {
                            pages.push(i);
                        }
                        pages.push('...');
                        pages.push(total);
                    } else if (current >= total - 3) {
                        pages.push(1);
                        pages.push('...');
                        for (let i = total - 4; i <= total; i++) {
                            pages.push(i);
                        }
                    } else {
                        pages.push(1);
                        pages.push('...');
                        for (let i = current - 1; i <= current + 1; i++) {
                            pages.push(i);
                        }
                        pages.push('...');
                        pages.push(total);
                    }
                }
                
                return pages;
            },
            
            startRecord: function() {
                if (!this.tours || this.tours.length === 0) return 0;
                return (this.currentPage - 1) * this.perPage + 1;
            },
            
            endRecord: function() {
                if (!this.tours) return 0;
                const end = this.currentPage * this.perPage;
                return end > this.tours.length ? this.tours.length : end;
            }
        },

        created: function () {
            this.fetchData();
        },

        methods: {
            fetchData: function () {
                var self = this;
                var userId = $('meta[name="user-id"]').attr('content');

                $.ajax({
                    url: '/api/v1/dashboard/tours',
                    method: 'GET',
                    data: {
                        'userId': userId
                    },
                    dataType: "json",
                    success: function (data) {
                        console.log('Tours loaded:', data);
                        self.tours = data.tours;
                        self.show = data.show;
                        self.edit = data.edit;
                        self.destroy = data.destroy;
                        self.loading = false;
                    },
                    error: function (error) {
                        console.log('Error loading tours:', error);
                        self.loading = false;
                    }
                });
            },
            
            changePage: function(page) {
                if (page < 1 || page > this.totalPages || page === '...') return;
                this.currentPage = page;
                document.querySelector('#tours').scrollIntoView({ behavior: 'smooth', block: 'start' });
            },
            
            showPaxFree: function (tour) {
                if (tour.pax_free !== '') {
                    return tour.pax_free;
                }
            },
            
            showTour: function (tour) {
                if (this.show) {
                    window.location.href = '/tour/' + tour.id;
                }
            },

            deleteTour: function(tour) {
                console.log('Delete tour clicked:', tour);
                this.tourToDelete = tour;
                
                // Set the tour name in modal
                $('#deleteTourName').text(tour.name + ' (ID: ' + tour.id + ')');
                
                // Show the modal
                $('#myModal').modal('show');
            },

            confirmDelete: function() {
                var self = this;
                
                if (!self.tourToDelete || !self.tourToDelete.id) {
                    alert('Error: No tour selected for deletion');
                    return;
                }

                var deleteUrl = '/tour/' + self.tourToDelete.id + '/delete';
                console.log('Deleting tour at URL:', deleteUrl);

                // Disable button and show loading state
                $('#confirmDeleteBtn').prop('disabled', true).text('Deleting...');

                $.ajax({
                    url: deleteUrl,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Delete successful:', response);
                        
                        // Close modal
                        $('#myModal').modal('hide');
                        $('.modal-backdrop').remove();
                        
                        // Show success message
                        alert('Tour deleted successfully!');
                        
                        // Reload the page
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Delete error:', xhr, status, error);
                        
                        // Close modal
                        $('#myModal').modal('hide');
                        $('.modal-backdrop').remove();
                        
                        // Reset button
                        $('#confirmDeleteBtn').prop('disabled', false).text('{{ trans("main.Delete") }}');
                        
                        var errorMsg = 'Error deleting tour!';
                        
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.error) {
                                errorMsg = xhr.responseJSON.error;
                            }
                        } else if (xhr.status === 404) {
                            errorMsg = 'Tour not found!';
                        } else if (xhr.status === 403) {
                            errorMsg = 'You do not have permission to delete this tour!';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Server error: ' + error;
                        }
                        
                        alert(errorMsg);
                    }
                });
            }
        }
    });

    // Handle confirm delete button click
    $('#confirmDeleteBtn').on('click', function() {
        // Get the Vue instance
        var vueInstance = document.getElementById('tours').__vue__;
        if (vueInstance && vueInstance.confirmDelete) {
            vueInstance.confirmDelete();
        }
    });

    // Clean up modal when closed
    $('#myModal').on('hidden.bs.modal', function () {
        $('#confirmDeleteBtn').prop('disabled', false).text('{{ trans("main.Delete") }}');
    });
});
</script>

<style>
.clickable-rows .clickable-row {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.clickable-rows .clickable-row:hover {
    background-color: #f8f9fa !important;
}

.clickable-rows .clickable-row td:last-child {
    cursor: default;
}

/* Pagination Styles */
.pagination-wrapper {
    margin-top: 20px;
    margin-bottom: 20px;
}

.pagination {
    margin-bottom: 10px;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.pagination .page-item.disabled .page-link {
    cursor: not-allowed;
    opacity: 0.5;
}

.pagination .page-link {
    cursor: pointer;
    color: #007bff;
    padding: 8px 12px;
    margin: 0 2px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.pagination .page-link:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.pagination-info {
    font-size: 14px;
    margin-top: 10px;
}
</style>