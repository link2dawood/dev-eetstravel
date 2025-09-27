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
                    <table class="table table-striped table-hover" style='background:#fff'>
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
                        <tr v-for="tour in tours">
                            <td v-on:click.passive="showTour(tour)">@{{tour['id']}}</td>
                            <td v-on:click.passive="showTour(tour)">@{{tour['name']}}</td>
                            <td v-on:click.passive="showTour(tour)">@{{tour['departure_date']}}</td>
                            <td v-on:click.passive="showTour(tour)">@{{tour['retirement_date']}}</td>
                            <td v-on:click.passive="showTour(tour)">@{{tour['pax']}} @{{showPaxFree(tour)}}</td>
                            <td v-on:click.passive="showTour(tour)">@{{tour['country_begin']}} -
                                @{{tour['city_begin']}}
                            </td>
                            <td v-on:click.passive="showTour(tour)">@{{tour['country_end']}} -
                                @{{tour['city_end']}}
                            </td>
                            <td v-on:click.passive="showTour(tour)">@{{tour['ga']}}</td>
                            <td v-on:click.passive="showTour(tour)">@{{tour['invoice']}}</td>
                            <td class="{{ \App\Helper\PermissionHelper::checkPermission('tour.edit') ? 'touredit-status' : '' }}"
                                :data-name-status="tour.status_name" :data-status-link="tour.status_link">
                                @{{tour['status_name']}}
                            </td>
                            <td v-on:click.passive="showTour(tour)">@{{tour['external_name']}}</td>
                            <td>
                                {{--     {!! \App\Helper\PermissionHelper::getActionsButton($tour,
                                    $url =
                                    [
                                    'show' => route('tour.show', ['tour' => $tour->id]),
                                    'edit' => route('tour.edit', ['tour' => $tour->id]),
                                    'deleteMsg' => "/tour/$tour->id/deleteMsg"
                                    ]) !!}--}}

                                <div style='width:150px; text-align: center;' class='buttons_margin'>
                                    <!-- INFO BUTTON-->
                                    <a v-if="show" :href="tour.routes.show"
                                       class='btn btn-warning btn-sm show-button'><i class="fa fa-info-circle"
                                                                                     aria-hidden="true"></i></a>
                                    <!-- EDIT BUTTON-->
                                    <a v-if="edit" :href="tour.routes.edit"
                                       class='btn btn-primary btn-sm'><i class="fa fa-pencil-square-o"
                                                                         aria-hidden="true"></i></a>
                                    <!-- DELETE BUTTON-->
                                    <a v-if="destroy" :data-link="tour.routes.delete_msg" data-toggle="modal"
                                       data-target="#myModal"
                                       class='btn btn-danger btn-sm delete'><i class="fa fa-trash-o"
                                                                               aria-hidden="true"></i></a>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box-footer clearfix">
                @if(Auth::user()->can('tour.create'))
                    <a href="{{route('tour.create')}}">
                        <button class="btn btn-primary pull-left" type="submit"><i class="fa fa-plus fa-md"
                                                                                            aria-hidden="true"></i> {{ trans('main.NewTour') }}
                        </button>
                    </a>
                @endif
                @if(Auth::user()->can('tour.index'))
                    <a href="{{route('tour.index')}}">
                        <button href="javascript:void(0)"
                                class="btn btn-default pull-right">{{ trans('main.ViewAllTours') }}
                        </button>
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


        new Vue({

            el: '#tours',

            data: {
                tours: null,
                show: false,
                edit: false,
                destroy: false,
                loading: true,
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
                            self.tours = data.tours;
                            self.show = data.show;
                            self.edit = data.edit;
                            self.destroy = data.destroy;
                            self.loading = false;
                        },
                        error: function (error) {
                            console.log(error);
                            self.loading = false;
                        }
                    });

                },
                showPaxFree: function (tour) {
                    if (tour.pax_free !== '') {
                        return tour.pax_free
                    }
                },
                showTour: function (tour) {
                    if (this.show) {
                        window.location.href = tour.routes.show;

                    }
                }

            }
        });

    });
</script>