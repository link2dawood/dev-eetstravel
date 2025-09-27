<div class="list-group">
    <div class="box box-solid">
        <br>
        @if(Auth::user()->can('tour_package.create'))
            <button class="btn btn-flat btn-success pull-right add-service-quick"
                    data-link="{{route('tour_package.store')}}" data-tour_id='{{$tour->id}}' data-tour_transfer="1"
                    data-service="transfer"
                    data-departure_date='{{$tour->departure_date}}'
                    data-retirement_date="{{$tour->retirement_date}}">{!!trans('main.AddBusCompany')!!}
            </button>
        @endif
        <h3 class="box-title">{!!trans('main.BusCompany')!!}</h3>
        <div class="alert alert-info block-error-driver" style="text-align: center; display: none;">

        </div>
        @if($tour->transfers)
            <table class="table table-striped table-bordered table-hover" style='background:#fff'>
                <colgroup>
                    <col style="width: auto;">
                    <col style="width: auto;">
                    <col style="width: auto;">
                    <col style="width: auto;">
                    <col style="width: auto;">
                    <col style="width: auto;">
                    <col style="width: auto;">
                    <col style="width: auto;">
                    <!--<col style="width: auto;">-->
                    <col style="width: auto;">
                </colgroup>
                <thead>
                <tr>
                    <th style="width: 15%; min-width: 100px;">{!!trans('main.Name')!!}</th>
                    <th>{!!trans('main.Status')!!}</th>
                    <th>{!!trans('main.Paid')!!}</th>
                    <th>{!!trans('main.Address')!!}</th>
                    <th>{!!trans('main.DriversPhones')!!}</th>
                    <th>{!!trans('main.DateFrom')!!}</th>
                    <th>{!!trans('main.Dateto')!!}</th>
                    <!--<th>Price for one</th>-->
                    <th>{!!trans('main.Actions')!!}</th>
                </tr>
                </thead>
                @foreach ($tour->transfers as $package)
                    <tr data-package_id='{{$package->id}}' data-type='{{@$package->service()->service_type}}'
                        data-is_main='@if(!$package->hasParrent()) {{ $package->main_hotel }} @else {{false}} @endif'
                        class="tour-package-item">
                        {{-- Check
                        <td valign="center" align="center" class="not-click">
                            <input type="checkbox" value="{{$package->id}}" class="export_selected_vch" checked>
                        </td> --}}
                        {{-- Name --}}
                        <td>
                        <span class="package-data" data-package_id='{{$package->id}}'
                              data-package-type="{{@$package->service()->service_type}}"
                              data-is_main='@if(!$package->hasParrent()) {{ $package->main_hotel }} @else {{false}} @endif'>
                            {{-- $package->name . ' (' .@$package->service()->service_type. ')' --}}
                            {{ $package->name }}<br>(Bus Company)

                        </span>
                        </td>
                        {{-- Status --}}
                        @if(!$statusesTransfers->isEmpty())
                            @php
                                $status = false;
                                $status_name = '';
                            @endphp
                            @foreach($statusesTransfers as $item)
                                @if($item->id == $package->status)
                                    <td class="{{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'tour_package_status' : '' }}"
                                        data-info-package-id="{{ $package->parent_id != null ? $package->parent_id  : $package->id }}"
                                        data-info-status="{{$item->name}}"
                                        data-info-status_id="{{$item->id}}"
                                        data-info-package-type="{{(@$package->service()->service_type === 'Hotel') ? 'hotel':  '' }}{{(@$package->service()->service_type === 'Transfer') ? 'transfer':  '' }}">
                                        {{$item->name}}</td>
                                    @php $status_name = $item->name; @endphp
                                    @php
                                        $status = true;
                                    @endphp
                                @else
                                @endif
                            @endforeach
                            @if (!$status)
                                <td></td>
                            @endif

                        @else
                            <td class="{{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'tour_package_status' : '' }}"
                                data-info-package-id="{{ $package->parent_id != null ? $package->parent_id  : $package->id }}"
                                data-info-status="{{$package->status}}"
                                data-info-status_id="{{$package->status->id}}"
                                data-info-package-type="{{(@$package->service()->service_type === 'Hotel') ? 'hotel':  '' }}{{(@$package->service()->service_type === 'Transfer') ? 'transfer':  '' }}">
                                {{$package->status}}
                            </td>
                        @endif
                        <td>
                            <button class="btn btn-xs {{$package->paid ? 'btn-success' : 'btn-danger'}}"
                                    type="button">{{$package->paid ? 'Yes' : 'No'}}</button>
                        </td>
                        <td>{{  @$package->service()->address_first}}</td>
                        <td>
                            @forelse($package->getTransferDrivers() as $driver)
                                <span style="display: block">{{ $driver->phone }}</span>
                            @empty
                            @endforelse
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($package->time_from)->format('Y-m-d')}}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($package->time_to)->format('Y-m-d')}}</td>

                        @if(!$package->hasParrent())
                            <td style="text-align: center;width:150px;">
                                {{-- Package service is hotel --}}
                                @if ($package->type == 0)
                                    <button class="{{ \App\Helper\PermissionHelper::checkPermission('comparison.show') ? 'main-hotel' : 'disabled' }} btn btn-xs {{$package->main_hotel ? 'btn-success' : 'btn-danger'}}"
                                            type="button" style="margin-bottom: 5px">M
                                    </button>
                                @endif
                                @if(Auth::user()->can('tour_package.edit'))
                                    <a href="/tour_package/{{$package->parent_id != null ? $package->parent_id  : $package->id}}/edit"
                                       class="btn btn-primary btn-xs show-button"
                                       data-link="/tour_package/{{$package->parent_id != null ? $package->parent_id  : $package->id}}/edit"
                                       style="margin-bottom: 5px"><i
                                                class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                @endif
                                @if(Auth::user()->can('tour_package.destroy'))
                                    <a data-toggle="modal" data-target="#myModal" class="delete btn btn-danger btn-xs"
                                       style="margin-bottom: 5px"
                                       data-link="/tour_package/{{$package->parent_id != null ? $package->parent_id  : $package->id}}/deleteMsg"><i
                                                class="fa fa-trash-o" aria-hidden="true"></i></a>
                                @endif
                                @if($package->parent_id == null)
                                    @if(Auth::user()->can('tour_package.create') && Auth::user()->can('tour_package.destroy'))
                                        <a href="#" class="btn btn-warning btn-xs open-modal-change-service"
                                           style="display: {{ @$package->getStatusName() !== 'Confirmed' ? 'inline-block' : 'none' }}; margin-bottom: 5px"
                                           data-toggle="modal"
                                           data-target="#list-tour-packages"
                                           data-serviceTypeId="{{ $package->type }}"
                                           data-serviceId="{{ @$package->service()->id }}"
                                           data-packageId="{{ $package->parent_id != null ? $package->parent_id  : $package->id }}"
                                           data-time-old-service="{{!$package->hasParrent() ? $package->time_from : null }}"
                                           data-info="page_change_main"
                                           data-tour_id="{{$package->getTour()->id}}"
                                           data-link="{{route('tour_package.store')}}"
                                        >
                                            <i class="fa fa-exchange" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                    @if(Auth::user()->can('tour_package.edit'))
                                        <a style="margin-bottom: 5px" href="javascript:void(0);"
                                           data-info="{{@$package ?: \GuzzleHttp\json_encode(' ')}}"
                                           onclick="loadTemplate(JSON.parse($(this).attr('data-info')) ? JSON.parse($(this).attr('data-info')).type : '','{!! @$package->service()->work_email !!}','{!! $package->name !!}','{!! $package->pax !!} {!! $package->pax_free !!}','{!!  @$package->service()->address_first !!}', '{!! @$package->service()->work_email !!}','{!! @$package->service()->work_phone !!}','{!! $package->description !!}','{!! $status_name !!}','{!! $package->time_from  !!}','','' );"
                                           class="btn btn-success btn-xs"
                                        ><i class="fa fa-envelope" aria-hidden="true"></i></a>
                                    @endif
                                @endif
                            </td>

                        @endif
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
    <?php $countDay = 0; ?>
    @foreach($tourDates as $tourDate)
        <?php $countDay++ ?>
        <div class="box box-solid">
            <div class="box-header with-border">
                @if(Auth::user()->can('tour_package.create'))
                    <button class="btn btn-flat btn-success pull-right add-service-quick"
                            data-tourDayId="{{$tourDate->id}}"
                            data-link="{{route('tour_package.store')}}" data-date="{!! $tourDate->date !!}"
                            data-tour_id='{{$tour->id}}'
                            data-departure_date='{{$tour->departure_date}}'
                            data-retirement_date="{{$tour->retirement_date}}">{!!trans('main.AddService')!!}
                    </button>
                    <button class="btn btn-flat btn-success pull-right add-description-package">{!!trans('main.Adddescription')!!}</button>
                @endif
                <h3 class="box-title">{!!trans('main.Day')!!} {{ $countDay }}
                    - {{ (new \Carbon\Carbon($tourDate->date))->formatLocalized('%B %d, %Y (%A)') }}</h3>
                <br/><br/>
                <div class="box-body">
                    <table class="table table-striped table-bordered table-hover {{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'package-service-table' : '' }}"
                           style='background:#fff'>
                        <colgroup>
                            <col style="width: auto">
                            <col style="width: auto">
                            <col style="width: auto">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <!--<col style="width: 30%;">
                            <col style="width: auto;">-->
                            <col style="width: auto;">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>Itn</th>
                            <th>Vch</th>
                            <th>{!!trans('main.FromTime')!!}</th>
                            <th></th>
                            <th style="width: 15%; min-width: 100px;">{!!trans('main.Name')!!}</th>
                            <th style="min-width: 150px">{!!trans('main.Status')!!}</th>
                            <th>{!!trans('main.Paid')!!}</th>
                            <th>Pax</th>
                            <th>Rooms</th>
                            <th>{!!trans('main.Address')!!}</th>
                            <th>{!!trans('main.Email')!!}</th>
                            <th>{!!trans('main.Phone')!!}</th>
                            <th>{!!trans('main.Description')!!}</th>
                        {{--<th>Price for one</th>--}}
                        <!-- <th style="width: 120px">Rooms Hotel</th>-->
                            <th style="width: 150px; min-width: 150px">{!!trans('main.Actions')!!}</th>
                        </tr>
                        </thead>
                        <tbody data-default_tour_day_id='{{$tourDate->id}}'>
                        @foreach($tourDate->packages as $package)
                            @if($package->description_package)
                                <tr data-package_id='{{$package->id}}' data-type='{{$package->type}}'
                                    data-is_main='@if(!$package->hasParrent()) {{ $package->main_hotel }} @else {{false}} @endif'>
                                    <td valign="center" align="center" class="not-click">
                                        @if($package->itn == 1)
                                        <input type="checkbox" value="{{$package->id}}" class="export_selected" checked>
                                        @else
                                        <input type="checkbox" value="{{$package->id}}" class="export_selected" >
                                        @endif
                                    </td>
                                    <td valign="center" align="center" class="not-click">
                                        {{--
                                        {{ $package->id }} is desc
                                        @if ($package->hasChild()) is has child : {{ $package->getChild()['id'] }}  {{ $package->getChild()['time_from'] }}  @else null  @endif
                                        @if ($package->hasParrent()) is has parent: {{  $package->parrent()['id'] }} {{  $package->parrent()['time_from'] }} @else null  @endif --}}
                                       @if($package->vch == 1)
                                        <input type="checkbox" value="{{$package->id}}" class="export_selected_vch"
                                               checked>
                                        @else
                                               <input type="checkbox" value="{{$package->id}}" class="export_selected_vch"
                                               >
                                        @endif
                                    </td>
                                    <td>
                                        <input style="width: 80px; background-color: inherit; border-style: none;"
                                               type="text"
                                               data-package_id="{{$package->id}}"
                                               class="form-control timepicker {{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'service-time' : '' }}"
                                               name="time_from"
                                               value="{!! $package->time_from !!}">
                                        <span class="package-data" data-type='{{$package->type}}'
                                              data-tour_day_id='{{$tourDate->id}}' data-package_id='{{$package->id}}'
                                              data-package-type="service_description"
                                              data-is_main='@if(!$package->hasParrent()) {{ $package->main_hotel }} @else {{false}} @endif'
                                        ></span>
                                    </td>
                                    <td colspan="9"
                                        class="{{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'service-description' : '' }}">{!! $package->description !!}</td>
                                    <td style="text-align: center">
                                        @if(Auth::user()->can('tour_package.destroy'))
                                            <a data-toggle="modal" data-target="#myModal"
                                               class="delete btn btn-danger btn-xs"
                                               data-link="/tour_package/{{$package->id}}/deleteMsg"
                                               style="text-align: center"><i class="fa fa-trash-o"
                                                                             aria-hidden="true"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if(!$package->description_package)
                                <tr data-package_id='{{$package->id}}'
                                    data-type='{{@$package->service()->service_type}}'
                                    data-is_main='@if(!$package->hasParrent()) {{ $package->main_hotel }} @else {{false}} @endif'
                                    class="tour-package-item">
                                    <td valign="center" align="center" class="not-click">
                                        @if($package->itn == 1)
                                        <input type="checkbox" value="{{$package->id}}" class="export_selected" checked>
                                        @else
                                        <input type="checkbox" value="{{$package->id}}" class="export_selected" >
                                        @endif
                                    </td>
                                    <td valign="center" align="center" class="not-click">

                                        @php
                                            $menu = '';
                                            $tourid = null ;
                                        @endphp
                                        @if ($package->type == 0 || $package->type == 4)
                                            @php
                                                if(count(@$package->service()->menus) > 0){
                                                    foreach(@$package->service()->menus as $men){
                                                     if ($men['id'] == $package->menu_id){
                                                            $menu = $men['name'];
                                                        }
                                                    }
                                                }
                                            @endphp
                                        @endif
                                        @if($package->vch == 1)
                                        <input type="checkbox" value="{{$package->id}}" class="export_selected_vch"
                                               checked>
                                        @else
                                               <input type="checkbox" value="{{$package->id}}" class="export_selected_vch"
                                               >
                                        @endif
                                    </td>
                                    <td class="not-click">
                                        @if(!$package->hasParrent())
                                            @if(\App\Helper\PermissionHelper::checkPermission('tour_package.edit'))
                                                <input style="width: 80px; background-color: inherit; border-style: none;"
                                                       type="text"
                                                       data-package_id="{{$package->id}}"
                                                       class="form-control timepicker service-time"
                                                       name="time_from"
                                                       value="{!! $package->time_from !!}"
                                                       data-type='{{@$package->service()->service_type}}'
                                                       data-is_main='@if(!$package->hasParrent()) {{ $package->main_hotel }} @else {{false}} @endif'
                                                >
                                            @else
                                                <span>{{ $package->time_from }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    {{-- Parent or child hotel--}}
                                    <td>
                                        @if(@$package->service()->service_type === 'Hotel')
                                            @if($package->parent_id)
                                                <i class="fa fa-star-o text-yellow"></i>
                                            @else
                                                <i class="fa fa-star text-yellow"></i>
                                            @endif

                                        @endif
                                    </td>
                                    <td><span class="package-data" data-tour_day_id='{{$tourDate->id}}'
                                              data-package_id='{{$package->id}}'
                                              data-package-type="{{@$package->service()->service_type}}"
                                              data-is_main='@if(!$package->hasParrent()) {{ $package->main_hotel }} @else {{false}} @endif'
                                        >{{$package->name  . ' (' .@$package->service()->service_type. ')'}}</span></td>
                                    @if(!$statusPackage->isEmpty())
                                        @php
                                            $status = false;
                                            $status_name = '';
                                        @endphp
                                        @foreach($statusPackage as $item)
                                            @if($item->id == $package->status)

                                                <td class="{{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'tour_package_status' : '' }}"
                                                    data-info-package-id="{{ $package->parent_id != null ? $package->parent_id  : $package->id }}"
                                                    data-info-status="{{$item->name}}"
                                                    data-info-status_id="{{$item->id}}"
                                                    data-info-package-type="{{(@$package->service()->service_type === 'Hotel') ? 'hotel':  '' }}{{(@$package->service()->service_type === 'Transfer') ? 'transfer':  '' }}">
                                                    {{$item->name}} </td>
                                                @php $status_name = $item->name; @endphp
                                                @php
                                                    $status = true;
                                                @endphp
                                            @else

                                            @endif
                                        @endforeach
                                        @if (!$status)
                                            <td></td>
                                        @endif

                                    @else
                                        <td class="{{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'tour_package_status' : '' }}"
                                            data-info-package-id="{{ $package->parent_id != null ? $package->parent_id  : $package->id }}"
                                            data-info-status="{{$package->status}}"
                                            data-info-status_id="{{$package->status->id}}"
                                            data-info-package-type="{{(@$package->service()->service_type === 'Hotel') ? 'hotel':  '' }}{{(@$package->service()->service_type === 'Transfer') ? 'transfer':  '' }}">
                                            {{$package->status}}
                                        </td>
                                    @endif
                                    <td>
                                        <button class="btn btn-xs {{$package->paid ? 'btn-success' : 'btn-danger'}}"
                                                type="button">{{$package->paid ? 'Yes' : 'No'}}</button>
                                    </td>
                                    <td>{{ $package->pax }} {{$package->pax_free}}</td>
                                    <td>
                                        @foreach($package->room_types_hotel as $item)
                                            <span>
                                                {{$item->room_types->code}}
                                                {{$item->count}}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>{{  @$package->service()->address_first}}</td>
                                    <td>{!! @$package->service()->work_email!!}</td>
                                    <td>{!! @$package->service()->work_phone!!}</td>
                                    <td class="{{ \App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'service-description' : '' }}">{!! $package->description !!}</td>
                                    {{--<td>{!! $package->total_amount !!}</td>--}}
                                    {{--<td>
                                        @foreach($package->room_types_hotel as $item)
                                            <span>
                                                {{ $item->room_types->code }}
                                                {{ $item->count }} - {{ $item->price }}
                                            </span>
                                            <br>
                                        @endforeach
                                    </td>--}}
                                    @if(!$package->hasParrent())
                                        <td style="text-align: center;width:150px;">
                                            {{-- Package service is hotel --}}
                                            @if ($package->type == 0)
                                                @php $tourid = $tour->id; @endphp
                                                <button class="{{ \App\Helper\PermissionHelper::checkPermission('comparison.show') ? 'main-hotel' : 'disabled' }} btn btn-xs {{$package->main_hotel ? 'btn-success' : 'btn-danger'}}"
                                                        type="button" style="margin-bottom: 5px">M
                                                </button>
                                            @endif
                                            @if(Auth::user()->can('tour_package.edit'))
                                                <a href="/tour_package/{{$package->parent_id != null ? $package->parent_id  : $package->id}}/edit"
                                                   class="btn btn-primary btn-xs show-button"
                                                   data-link="/tour_package/{{$package->parent_id != null ? $package->parent_id  : $package->id}}/edit"
                                                   style="margin-bottom: 5px"><i
                                                            class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                            @endif
                                            @if(Auth::user()->can('tour_package.destroy'))
                                                <a data-toggle="modal" data-target="#myModal"
                                                   class="delete btn btn-danger btn-xs" style="margin-bottom: 5px"
                                                   data-link="/tour_package/{{$package->parent_id != null ? $package->parent_id  : $package->id}}/deleteMsg"><i
                                                            class="fa fa-trash-o" aria-hidden="true"></i></a>
                                            @endif
                                            @if($package->parent_id == null)
                                                @if(Auth::user()->can('tour_package.create') && Auth::user()->can('tour_package.destroy'))
                                                    <a href="#" class="btn btn-warning btn-xs open-modal-change-service"
                                                       style="display: {{ @$package->getStatusName() !== 'Confirmed' ? 'inline-block' : 'none' }}; margin-bottom: 5px"
                                                       data-toggle="modal"
                                                       data-target="#list-tour-packages"
                                                       data-serviceTypeId="{{ $package->type }}"
                                                       data-serviceId="{{ @$package->service()->id }}"
                                                       data-packageId="{{ $package->parent_id != null ? $package->parent_id  : $package->id }}"
                                                       data-tour-day-id="{{$tourDate->id}}"
                                                       data-tour_id="{{$package->getTour()->id}}"
                                                       data-time-old-service="{{!$package->hasParrent() ? $package->time_from : null }}"
                                                       data-link="{{route('tour_package.store')}}"
                                                    >
                                                        <i class="fa fa-exchange" aria-hidden="true"></i>
                                                    </a>
                                                @endif

                                                @if(Auth::user()->can('tour_package.edit'))
                                                    @if(@$package->service()->work_email)
                                                        <a style="margin-bottom: 5px" href="javascript:void(0);"
                                                           data-info="{{@$package ?: \GuzzleHttp\json_encode(' ')}}"
                                                           onclick="loadTemplate(JSON.parse($(this).attr('data-info')) ? JSON.parse($(this).attr('data-info')).type : '','{!! @$package->service()->work_email !!}','{!! $package->name !!}','{!! $package->pax !!} {!! $package->pax_free !!}','{!!  @$package->service()->address_first !!}', '{!! @$package->service()->work_email !!}','{!! @$package->service()->work_phone !!}','{!! $package->description !!}','{!! $status_name !!}','{!! $package->time_from  !!}','{!! $package->time_to 
                                                           !!}','{!! $package->supplier_url 
                                                           !!}','{!! $package->total_amount !!}','{{ $menu }}','{{ $tourid }}');"
                                                           class="btn btn-success btn-xs"
                                                        ><i class="fa fa-envelope" aria-hidden="true">{{$package->supplier_url }}</i>  </a>
                                                        <i class="fa fa-exchange" aria-hidden="true"></i>
                                                    @endif
                                                @endif
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="modal fade" tabindex="-1" id="list-tour-packages" style="padding-left: 17px;padding-right: 17px;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content" style="overflow: hidden;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span aria-hidden='true'>&times;</span>
                </button>
                <h4 class="modal-title">{!!trans('main.Changeservice')!!}</h4>
            </div>
            <div class="box box-body" style="border-top: none">
                <table id="search-table-service-list" class="table table-striped table-bordered table-hover"
                       style="width: 100%!important;">
                    <thead>
                    <tr>
                        <th>{!!trans('main.Name')!!}</th>
                        <th>{!!trans('main.Address')!!}</th>
                        <th>{!!trans('main.Country')!!}</th>
                        <th>{!!trans('main.City')!!}</th>
                        <th>{!!trans('main.Phone')!!}</th>
                        <th>{!!trans('main.ContactName')!!}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" tabindex="-1" id="select-driver-and-bus">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_transfer_buses_drivers">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title">{!!trans('main.Selectdriversandbuses')!!}</h4>
                </div>
                <div class="box box-body" style="border-top: none">
                    <div class="list-driver-and-buses"></div>

                    <div class="modal-footer">
                        <div class="btn-send-driver">
                            <button type="button"
                                    class="btn btn-success btn-send-transfer_add pre-loader-func">{!!trans('main.Add')!!}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="select-driver-and-bus_transfer_package">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_transfer_buses_drivers_transfer_package">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title">Select drivers and buses</h4>
                </div>
                <div class="box box-body" style="border-top: none">
                    <div class="list-driver-and-buses_transfer_package"></div>

                    <div class="modal-footer">
                        <div class="btn-send-driver">
                            <button type="button"
                                    class="btn btn-success btn-send-transfer_add_transfer_package pre-loader-func">{!!trans('main.Add')!!}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


{{--HOtel Modal Confirm Status--}}
<div class="modal fade" tabindex="-1" id="confirmed_hotel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_confirmed_hotel">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title">{!!trans('main.Confirmedhotel')!!}</h4>
                </div>
                <div class="modal-body">
                    <div class="confirmed_hotel_block">

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-send-confirmed_hotel">
                        <button type="button"
                                class="btn btn-success btn-send-confirmed_hotel_add">{!!trans('main.Save')!!}</button>
                        <button type="button"
                                class="btn btn-default btn-send-hotel_cancel">{!!trans('main.Cancel')!!}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{--HOtel Error Tour message--}}
<div class="modal fade" tabindex="-1" id="error_hotel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_eror_hotel">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title">{!!trans('main.Warning')!!}!</h4>
                </div>
                <div class="modal-body">
                    <div class="confirmed_hotel_block">
                        <h3 id="message"></h3>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-error-hotel">
                        <button type="button" class="btn btn-success " id="ok">Ok</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{--Hotel Modal Date--}}
<div class="modal fade" id="selectDateForHotelPackage" tabindex="-1" aria-labelledby='selectDateForHotelPackageLabel'>
    <div class="modal-dialog modal-lg" role='document'>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span>
                </button>
                <h4 class="modal-title">{!!trans('main.SelectDate')!!}</h4>
            </div>
            <div class="box box-body" style="border-top: none">

                <div class="alert alert-info error_date" style="text-align: center; display: none;">

                </div>

                <div class="form-group">

                    <label for="departure_date">{!!trans('main.DateFrom')!!}</label>

                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        {!! Form::text('date_service_package', '', ['class' => 'form-control pull-right datepickerDisabledHotelPackage',
                         'id' => 'date_service_package']) !!}
                    </div>

                </div>

                <div class="form-group">

                    <label for="departure_date">{!!trans('main.Dateto')!!}</label>

                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        {!! Form::text('date_service_retirement_package', '', [
                        'class' => 'form-control pull-right datepickerDisabledHotelPackage',
                        'id' => 'date_service_retirement_package'
                        ]) !!}
                    </div>

                </div>
                <button class="addHotelPackageWithDate pre-loader-func btn btn-success"
                        type="button">{!!trans('main.Add')!!}</button>
            </div>
        </div>
    </div>
</div>


{{-- Transfer Modal Date--}}
<div class="modal fade" id="selectDateForTransferPackage" aria-labelledby='selectDateForTransferPackageLabel'>
    <div class="modal-dialog modal-lg" role='document'>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span>
                </button>
                <h4 class="modal-title">{!!trans('main.SelectDate')!!}</h4>
            </div>
            <div class="modal-body">

                <div class="alert alert-info error_date" style="text-align: center; display: none;">

                </div>


                <div class="form-group">

                    <label for="departure_date">{!!trans('main.DateFrom')!!}</label>

                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        {!! Form::text('date_service_package', '', ['class' => 'form-control pull-right datepickerDisabledTransferPackage',
                         'id' => 'date_service_transfer_package']) !!}
                    </div>

                </div>

                <div class="form-group">

                    <label for="departure_date">{!!trans('main.DateTo')!!}</label>

                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        {!! Form::text('date_service_retirement_package', '', [
                        'class' => 'form-control pull-right datepickerDisabledTransferPackage',
                        'id' => 'date_service_transfer_retirement_package'
                        ]) !!}
                    </div>

                </div>
                <div style="overflow: hidden; display: block">
                    <button class="addTransferPackageWithDate btn btn-success pull-right"
                            type="button">{!!trans('main.Next')!!}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="TemplatesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="false" style="padding-left: 17px;padding-right: 17px;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <form class="modal-content" id="templateSendForm" enctype="multipart/form-data" action="/templates/api/send"
              method="POST">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <input name="id" id="id" type="hidden" value="">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{!!trans('main.SendTemplate')!!}</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">

                    <div class="form-group">
                        <div class="input-group">
                            <input name="email" id="email" class="form-control" placeholder="E-mail:" required=""
                                   value="">
                            <span class="input-group-addon"> {!!trans('main.Template')!!}</span>
                            <!-- insert this line -->
                            <span class="input-group-addon"
                                  style="width:0px; padding-left:0px; padding-right:0px; border:none;"></span>

                            <select id="template_selector" name="template_selector" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <input name="subject" id="subject" class="form-control" placeholder="Subject:" value="">
                    </div>
                    <div class="form-group">
                            <textarea name="templatesContent" id="templatesContent" placeholder="Non required Field"
                                      class="form-control" style="height: 400px; visibility: hidden; display: none;">
                            </textarea>
                    </div>
                    <div class="form-group">
                        <div class="btn btn-default btn-file">
                            <i class="fa fa-paperclip"></i> {!!trans('main.Attachment')!!}
                            <input type="file" name="attachment[]" multiple="" name="file" id="file">
                        </div>
                        <div id="file_name"></div>
                        <script>
                            document.getElementById('file').onchange = function () {
                                $('#file_name').html('Selected files: <br/>');
                                $.each(this.files, function (i, file) {
                                    $('#file_name').append(file.name + ' <br/>');
                                });
                            };
                        </script>
                        <p class="help-block">Max. 32MB</p>
                    </div>
                </div>

                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="pull-right">
                        <button id="send" onclick="sendTemplate();" class="btn btn-primary"><i
                                    class="fa fa-file-code-o"></i> {!!trans('main.Send')!!}</button>
                    </div>
                    <button type="reset" class="btn btn-default modal-close" data-dismiss="modal"><i
                                class="fa fa-times"></i> {!!trans('main.Discard')!!}</button>
                </div>
                <!-- /.box-footer -->
            </div>

        </form>
    </div>
</div>

<span id="last_package_object" data-info="{{ @$package ?
 @$package->description_package == null ? @$package->type : \GuzzleHttp\json_encode('') :
    \GuzzleHttp\json_encode('') }}"></span>
<script type="text/javascript">


    var unchecked_array = [];
    var unchecked_array_vch = [];

    $(document).ready(function () {
        // $('input.timepicker').timepicker();
        finder.init();
        addService.checkTransferIsExist();
        let package_type = JSON.parse($('#last_package_object').attr('data-info'));
        if (package_type) {
            loadGuestTemplate(package_type, "{!! @$package ? @$package->service()->work_email : '' !!}", "{!! @$package->name !!}", "{!! @$package->pax !!} {!! @$package->pax_free !!}", "{!!  @$package ? @$package->service()->address_first : ''!!}", "{!! @$package ? @$package->service()->work_email : ''!!}", "{!! @$package ? @$package->service()->work_phone  : ''!!}", `{!! @$package->description !!}`, "{!! @$status_name !!}", "{!! @$package->time_from  !!}", "{!! @$package->total_amount !!}", "{{ @$menu }}", "{{ @$tourid }}");
        }
        // $('.timepicker').datetimepicker({
        //     format: 'HH:mm', 'sideBySide' : true,
        //     tooltips: {
        //         incrementHour: '',
        //         pickHour: '',
        //         decrementHour:'',
        //         incrementMinute: '',
        //         pickMinute: '',
        //         decrementMinute:'',
        //         incrementSecond: '',
        //         pickSecond: '',
        //         decrementSecond:'',

        //     }


        // }).on("dp.hide", function (e) {
        //      let timeKey = $(this).attr('name');
        //      console.log($(this).attr('name'));
        //      // $(this).datetimepicker('hide');
        //      $.ajax({
        //         url: '/tour_package/' + $(this).data('package_id') + '/change_time',
        //         method: 'GET',
        //         data: {
        //             timeKey: timeKey,
        //             timeValue: $(this).val()
        //         }
        //      }).done( (res) => {
        //         addService.addPackages();
        //      })
        //      // return false;
        // });

        if ($(document).find('#templatesContent').length > 0) {
            if (CKEDITOR.instances['templatesContent']) {
                CKEDITOR.instances['templatesContent'].destroy(true);
            }
            CKEDITOR.replace('templatesContent', {
                extraPlugins: 'confighelper',
                height: '200px',
                title: false
            });
        }

        $('.export_selected').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        }).on('ifClicked', function (e) {

            $(this).on('ifUnchecked', function (event) {

                unchecked_array[event.target.value] = event.target.value;
				$.ajax({
                    type: 'POST', // Use POST or GET as needed
                    url: '/update_itnid', // Replace with the actual URL that handles the update
                    data: {
                    id:event.target.value ,
                    value:0,
                    
                    },
                });

            });

            $(this).on('ifChecked', function (event) {

                delete unchecked_array[event.target.value];
				$.ajax({
                    type: 'POST', // Use POST or GET as needed
                    url: '/update_itnid', // Replace with the actual URL that handles the update
                    data: {
                    id:event.target.value ,
                    value:1,
                    
                    },
                });

            });

        });

        $('.export_selected_vch').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        }).on('ifClicked', function (e) {

            $(this).on('ifUnchecked', function (event) {

                unchecked_array_vch[event.target.value] = event.target.value;
				$.ajax({
                    type: 'POST', // Use POST or GET as needed
                    url: '/update_voucherid', // Replace with the actual URL that handles the update
                    data: {
                    id:event.target.value ,
                    value:0,
                    
                    },
                });

            });

            $(this).on('ifChecked', function (event) {

                delete unchecked_array_vch[event.target.value];
				$.ajax({
                    type: 'POST', // Use POST or GET as needed
                    url: '/update_voucherid', // Replace with the actual URL that handles the update
                    data: {
                    id:event.target.value ,
                    value:1,
                    
                    },
                });

            });

        });

    });
    export_to = function (url) {

        console.log(url);

        var out = '?';

        for (var i = 0; i < unchecked_array.length; i++) {
            if (unchecked_array[i]) out += "exclude[]=" + unchecked_array[i] + "&";

        }

        for (var i = 0; i < unchecked_array_vch.length; i++) {
            if (unchecked_array_vch[i]) out += "exclude_vch[]=" + unchecked_array_vch[i] + "&";

        }


        out = out.slice(0, -1);

        //console.log(url+"/"+out);
        if (url.indexOf("landingpage") > 0) {
            $('#landingpage_modal').modal('hide');
            window.open(
                url + "/" + out,
                '_blank'
            )
        } else
            window.location.href = url + "/" + out;

    };

    sendTemplate = function () {

        $('form#templateSendForm').submit(function (event) {

            event.preventDefault();

            $('#TemplatesModal').find('#send').prop('disabled', true);

            var data = new FormData($(this)[0]);

            $.ajax({
                url: '/templates/api/send',
                method: 'POST',
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                data: data
            }).done((res) => {
                console.log(res);
                $('#TemplatesModal').find('#email').val('');
                $('#TemplatesModal').find('#subject').val('');
//                CKEDITOR.instances['templatesContent'].setData('This field is optional');
                $('#TemplatesModal').modal('hide');
            });

            return false;
        });

    };


    loadTemplateById = function (service_id, email, name, pax, address, emailto, phone, description, status, time_from,time_to,supplier_url, price_for_one, menu, tour_id) {
alert(supplier_url);
        var id = $('#TemplatesModal').find('#template_selector').val();

        $.ajax({
            url: '/templates/api/load',
            method: 'GET',
            data: {
                service_id: service_id,
                id: id,
                email: email,
                name: name,
                pax: pax,
                address: address,
                emailto: emailto,
                phone: phone,
                description: description,
                status: status,
                time_from: time_from,
                time_to: time_to,
                supplier_url: supplier_url,
                price_for_one: price_for_one,
                menu: menu,
                tour_id: tour_id
            }
        }).done((res) => {
            $('#TemplatesModal').find('#email').val(res.email);
            $('#TemplatesModal').find('#id').val(id);
//            if(res.content === '' ) res.content = 'This field is optional';

            CKEDITOR.instances['templatesContent'].setData(res.content);
            $('#TemplatesModal').modal('show');
        });

    };


    loadTemplate = function (service_id, email, name, pax, address, emailto, phone, description, status, time_from,time_to,supplier_url ,price_for_one, menu, tour_id) {
alert(supplier_url);
console.log(`Supplier Url ${supplier_url}`);
        var selected = '';
        var html = '';

        $('#TemplatesModal').find('#send').prop('disabled', false);
        $('#TemplatesModal').find('#email').val('');
        $('#TemplatesModal').find('#subject').val('');
        $('#TemplatesModal').find('#file').val('');
        $('#TemplatesModal').find('#file_name').text('');
        CKEDITOR.instances['templatesContent'].setData('');

        $.ajax({
            url: '/templates/api/loadServiceTemplates',
            method: 'GET',

            data: {
                id: service_id,
            },
            success: function (res) {

                for (var i = 0; i < res.templates.length; i++) {

                    (i == 0) ? selected = "selected" : "";

                    if (res.templates[i]['name'] != 'Footer' && res.templates[i]['name'] != 'Header') {
                        html += "<option value='" + res.templates[i]['id'] + "' " + selected + ">" + res.templates[i]['name'] + "</option>";
                    }
                }

                $('#TemplatesModal').find('#template_selector').html(html);

                $('#TemplatesModal').find('#template_selector').on('change', function () {
                    loadTemplateById(service_id, email, name, pax, address, emailto, phone, description, status, time_from,time_to,supplier_url, price_for_one, menu, tour_id);
                });

                loadTemplateById(service_id, email, name, pax, address, emailto, phone, description, status, time_from, time_to,supplier_url,price_for_one, menu, tour_id);
            }

        })

    };


    function loadGuestTemplate(service_id, email, name, pax, address, emailto, phone, description, status, time_from, price_for_one, menu, tour_id) {
        var selected = '';
        var html = '';

//        $('#TemplatesModal').find('#send').prop('disabled', false);
//        $('#TemplatesModal').find('#email').val('');
//        $('#TemplatesModal').find('#subject').val('');
//        $('#TemplatesModal').find('#file').val('');
//        $('#TemplatesModal').find('#file_name').text('');
//        CKEDITOR.instances['templatesContent'].setData('');

        $.ajax({
            url: '/templates/api/loadServiceTemplates',
            method: 'GET',

            data: {
                id: 0,
            },
            success: function (res) {

                html += "<option value='' selected disabled hidden>Choose here</option>";
                for (var i = 0; i < res.templates.length; i++) {

                    //(i == 0) ? selected = "selected" : "";

                    if (res.templates[i]['name'] != 'Footer' && res.templates[i]['name'] != 'Header') {
                        html += "<option value='" + res.templates[i]['id'] + "' " + selected + ">" + res.templates[i]['name'] + "</option>";
                    }
                }

                $('#template_selector_guest').html(html);

                $('#template_selector_guest').on('change', function () {
                    var id = $('#template_selector_guest').val();

                    $.ajax({
                        url: '/templates/api/load',
                        method: 'GET',
                        data: {
                            service_id: service_id,
                            id: id,
                            email: email,
                            name: name,
                            pax: pax,
                            address: address,
                            emailto: emailto,
                            phone: phone,
                            description: description,
                            status: status,
                            time_from: time_from,
                            price_for_one: price_for_one,
                            menu: menu,
                            tour_id: tour_id
                        }
                    }).done((res) => {
                        CKEDITOR.instances['roomlist_textarea'].setData(res.content);
                    });
                });
            }
        })
    }

</script>
