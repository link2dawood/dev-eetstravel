<div class="col-md-12">
    {{--    Activities Table--}}
    <div class="box box-primary">
        @if(Auth::user()->can('dashboard.activities'))
            <div class="box-header">
                <h4>{{ trans('main.Activities') }}</h4>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div id="activities">
                    <div v-if="loading">
                        <div class="box-body" style="height: 120px;">
                            <div class="loader"></div>

                        </div>
                    </div>
                    <div v-else>
                        <table class="table table-striped table-hover" style='background:#fff'
                               id="activities">
                            <thead>
                            <th>{{ trans('main.CreatedTime') }}</th>
                            <th>{{ trans('main.Usercreated') }}</th>
                            {{-- <th>Action</th> --}}
                            <th>{{ trans('main.Description') }}</th>
                            <th>{{ trans('main.Actions') }}</th>
                            </thead>
                            <tbody>
                            <tr v-for="log in logs" v-on:click.passive="showActivity(log)">
                                <td> @{{log['created_at']}}</td>
                                <td>@{{log['causer']['name']}}</td>
                                <td>
                                    @{{log['description']}}
                                </td>
                                <td>
                                    <a v-if="log.properties.link" class="btn btn-warning btn-sm pull-right"
                                       :href="log.properties.link"><i
                                                class='fa fa-info-circle'></i></a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    @if(Auth::user()->can('activities.index'))
                        <div class="box-footer clearfix">
                            <a href="{{route('activities_index')}}">
                                <button href="javascript:void(0)"
                                        class="btn btn-default pull-right">{{ trans('main.ViewAllActivities') }}
                                </button>
                            </a>
                        </div>
                    @endif
                </div>
                @else
                    <div class="box-header">
                        <h4>{{ trans('main.Activities') }}</h4>
                    </div>
                    <div class="box-body">
                        {{ trans('main.Youdonthavepermissions') }}
                    </div>
                @endif
            </div>
    </div>
</div>

<script>

    $(function () {


        new Vue({

            el: '#activities',

            data: {
                logs: null,
                loading: true,
            },

            created: function () {
                this.fetchData();
            },

            methods: {
                fetchData: function () {
                    var self = this;

                    $.ajax({
                        url: '/api/v1/dashboard/activities',
                        method: 'GET',
                        dataType: "json",
                        success: function (data) {
                            self.logs = data.data;
                            self.loading = false;
                        },
                        error: function (error) {
                            console.log(error);
                            self.loading = false;

                        }
                    });

                },
                showActivity: function (log) {
                    if (log.properties.link) {
                        window.location.href = log.properties.link;
                    }
                }

            }
        });

    });
</script>