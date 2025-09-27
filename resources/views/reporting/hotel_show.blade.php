@extends('scaffold-interface.layouts.app')
@section('title', 'Show')
@section('content')
    @include('layouts.title', [
        'title' => 'Reporting',
        'sub_title' => 'Show',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'accountings', 'icon' => 'handshake-o', 'route' => route('accounting.index')],
            ['title' => 'Show', 'route' => null],
        ],
    ])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="margin_button">
                            <a href="javascript:history.back()">
                                <button class='btn btn-primary'>{!! trans('main.Back') !!}</button>
                            </a>

                            {{--
						<a href="{!! route('accounting.edit', $transactions->id) !!}">
                            <button class='btn btn-warning'>{!!trans('main.Edit')!!}</button>
                        </a> --}}
                        </div>
                    </div>
                </div>
                <div id="fixed-scroll" class="nav-tabs-custom">
                    <ul class="nav nav-tabs" id="fixed-scroll" role='tablist'>
                        <li role='presentation' class="active"><a href="#info-tab" aria-controls='info-tab' role='tab'
                                data-toggle='tab'>{!! trans('main.Info') !!}</a></li>

                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>

                        <input id="tourName" type="hidden" name="tourName" value="">
                        <table class='table table-bordered' style="width:50%; float:left">
                            <tbody>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.Name') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->name !!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.AddressFirst') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->address_first !!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.AddressSecond') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->address_second !!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.Code') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->code !!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.Country') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! \App\Helper\CitiesHelper::getCountryById($hotel->country)['name'] !!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.City') !!} : </i></b>
                                    </td>
                                    @if (!empty($hotel->city))
                                        <td class="info_td_show">{!! \App\Helper\CitiesHelper::getCityById($hotel->city)['name'] !!}</td>
                                    @else
                                        <td class="info_td_show"></td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.WorkPhone') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->work_phone !!}</td>
                                </tr>

                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.WorkFax') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->work_fax !!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.WorkEmail') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->work_email !!}</td>
                                </tr>
                            </tbody>
                        </table>
                        <table class='table table-bordered' style="width:50%; float:left">
                            <tbody>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.ContactName') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->contact_name !!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.ContactPhone') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->contact_phone !!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.ContactEmail') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->contact_email !!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.Comments') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->comments !!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.IntComments') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->int_comments !!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.Criterias') !!} : </i></b>
                                    </td>
                                    <?php
                                    $empty = 0;
                                    ?>
                                    @forelse($criterias as $criteria)
                                        @forelse($hotel->criterias as $item)
                                            @if ($criteria->id == $item->criteria_id)
                                                <td class="info_td_show criteria_block" style="width:100%">
                                                    {!! $criteria->name !!}</td>
                                                <?php
                                                $empty = 1;
                                                ?>

                                                {{--                                                <td class="info_td_show">{!!$criteria->name!!}</td> --}}
                                            @endif
                                        @empty
                                            {{--                                            <td class="info_td_show"></td> --}}
                                        @endforelse
                                    @empty
                                        {{--                                        <td class="info_td_show"></td> --}}
                                    @endforelse
                                    @if ($empty == 0)
                                        <td class="info_td_show"></td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.Rate') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->rate_name !!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.Website') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->website !!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.CityTax') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->city_tax !!}</td>
                                </tr>

                                <tr>
                                    <td>
                                        <b><i>{!! trans('main.Note') !!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! $hotel->note !!}</td>
                                </tr>

                            </tbody>
                        </table>

                        <div style="clear: both"></div>

                        <h1 style="font-size: 16px; margin-bottom: 6px;">Total invoice Amount</h1>
                        <h3 style="font-size: 50px; font-weight: 700; color: black;">€
                            {{ number_format($invoice_total, 0, '.', ',') }} </h3>
                        <input type="hidden" value="{{ $totalAmounts['Febuary'] }}" id="value10">
                        <input type="hidden" value="{{ $totalAmounts['March'] }}" id="value20">
                        <input type="hidden" value="{{ $totalAmounts['April'] }}" id="value30">
                        <input type="hidden" value="{{ $totalAmounts['May'] }}" id="value40">
                        <input type="hidden" value="{{ $totalAmounts['June'] }}" id="value50">

                        <canvas id="chart" class="chart" style="max-height: 500px;"></canvas>
                    </div>
                    <div class="tab-pane fade" role='tabpanel' id='client-transactions-tab'>
                        <div class="box box-primary" style='margin-bottom: 20px;'>
                            <div class="box-body">


                            </div>
                        </div>
                    </div>


                </div>
            </div>
    </section>

@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>

@endsection
@push('scripts')
    <script type="text/javascript" src="{{ asset('js/jspdf.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var currentDate = new Date();
        var monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        var currentMonth = monthNames[currentDate.getMonth()];
        var previousMonths = [];
        for (let i = 4; i >= 0; i--) {

            var previousMonthIndex = currentDate.getMonth() - i;
            previousMonths.push(monthNames[previousMonthIndex < 0 ? 11 : previousMonthIndex]);


        }
        //previousMonths.push(currentMonth);


        var day = currentDate.getDate();

        const ctx = document.querySelectorAll('.chart');

        for (var i = 0; i < ctx.length; i++) {
            var value1 = document.getElementById("value1" + i).value;
            var value2 = document.getElementById("value2" + i).value;
            var value3 = document.getElementById("value3" + i).value;
            var value4 = document.getElementById("value4" + i).value;
            var value5 = document.getElementById("value5" + i).value;



            new Chart(ctx[i], {
                type: "line",
                data: {
                    labels: previousMonths,
                    datasets: [{
                        label: "Amount",
                        data: [value1, value2, value3, value4, value5],
                        borderWidth: 1,
                        borderColor: "#159a9c",
                        // pointRadius: 0,
                        backgroundColor: '#159a9c',
                    }, ],
                },
                options: {
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        x: {
                            display: true
                        },
                        y: {
                            beginAtZero: true,
                            display: true
                        },
                    },
                },
            });

        }
    </script>
@endpush
