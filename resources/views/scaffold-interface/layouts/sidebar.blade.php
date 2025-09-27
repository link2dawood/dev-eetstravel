<section class="sidebar">
    @php
        $route = \Route::currentRouteName();
    @endphp
    <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <li class="treeview {{App\Helper\DashboardHelper::isMenuActive('dashboard_main', $route)}}">
            <a href="{{url('home')}}">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span></i>
            </a>
        </li>
        <li class="header">{{ trans('main.Usersadministration') }}</li>

        @if(Auth::user()->hasRole('admin'))
        <li class="treeview">
            <a href="#"><i class="fa fa-angle-left pull-right"></i><i class='fa fa-users'></i> <span>{{ trans('main.Users') }}</span> </a>
            <ul class="treeview-menu
                    @if (\App\Helper\DashboardHelper::isMenuTreeViewActive(['users.index', 'roles.index', 'permissions.index', 'setting.index'], $route))
                    menu-open
                    @endif
            "
                @if (\App\Helper\DashboardHelper::isMenuTreeViewActive(['users.index', 'roles.index', 'permissions.index', 'setting.index'], $route))
                style="display: block"
                    @endif
            >
                <li class="treeview {!! \App\Helper\DashboardHelper::isMenuActive('users.index', $route) !!}"><a href="{{url('/users')}}"><i class="fa fa-users"></i> <span>{{ trans('main.Users') }}</span></a></li>
                <li class="treeview {!! \App\Helper\DashboardHelper::isMenuActive('roles.index', $route) !!}"><a href="{{url('/roles')}}"><i class="fa fa-user-plus"></i> <span>{{ trans('main.Roles') }}</span></a></li>
                <li class="treeview {!! \App\Helper\DashboardHelper::isMenuActive('permissions.index', $route) !!}"><a href="{{url('/permissions')}}"><i class="fa fa-key"></i> <span>{{ trans('main.Permissions') }}</span></a></li>
                <li class="{{ App\Helper\DashboardHelper::isMenuActive('setting.index',$route)}}">
                            <a href="{{route('settings.index')}}">
                                <i class="fa fa-cog" aria-hidden='true'></i>
                                <span>{{trans('Settings')}}</span>
                            </a>
                        </li>
            </ul>
        </li>
        @endif

        <li class="header">{{ trans('main.TourManagement') }}</li>

       
{{--
        @if(Auth::user()->can('quotation.index'))
            <li class="{!! \App\Helper\DashboardHelper::isMenuActive('quotation.index',$route) !!}"><a href="{{route('quotation.index')}}"><i class='fa fa-indent'></i> <span> Tour Quotations</span></a></li>
        @endif
--}}
		 @if(Auth::user()->can('tour.index'))
            <li class="{!! \App\Helper\DashboardHelper::isMenuActive('tour.index',$route) !!}"><a href="{{route('tour.index')}}"><i class='fa fa-suitcase'></i> <span>{{ trans('main.Tours') }}</span></a></li>
        @endif

        @if(Auth::user()->can('quotation.index'))
			 <li class="treeview">
				 <a href="#"><i class="fa fa-angle-left pull-right"></i><i class='fa fa-map-pin'></i> <span>{{ trans('Offers') }}</span> </a>
				 <ul class="treeview-menu">			
            <li class="{!! \App\Helper\DashboardHelper::isMenuActive('current_offers.index',$route) !!}"><a href="{{route('current_offers.index')}}"><i class='fa fa-indent'></i> <span>{{ trans('Current Offers') }}</span></a></li>
					  <li class="{!! \App\Helper\DashboardHelper::isMenuActive('past_offers.index',$route) !!}"><a href="{{route('past_offers.index')}}"><i class='fa fa-indent'></i> <span>{{ trans('Past Offers') }}</span></a></li>
					 <li class="{!! \App\Helper\DashboardHelper::isMenuActive('current_bookings.index',$route) !!}"><a href="{{route('current_bookings.index')}}"><i class='fa fa-indent'></i> <span>{{ trans('Current Bookings') }}</span></a></li>
					 <li class="{!! \App\Helper\DashboardHelper::isMenuActive('cancellation_policies.index',$route) !!}"><a href="{{route('cancellation_policies.index')}}"><i class='fa fa-indent'></i> <span>{{ trans('Cancellation Policies') }}</span></a></li>
				 </ul>
			</li>
        @endif

        @if(Auth::user()->can('task.index'))
        <li class="{!! \App\Helper\DashboardHelper::isMenuActive('task.index',$route) !!}">
            <a href="{{route('task.index')}}"><i class='fa fa-tasks'></i> <span>{{ trans('main.Tasks') }}</span></a></li>
        @endif

        <li class="treeview">
            <a href="#"><i class="fa fa-angle-left pull-right"></i><i class='fa fa-map-pin'></i> <span>{{ trans('main.Services') }}</span> </a>
            <ul class="treeview-menu
                    @if (\App\Helper\DashboardHelper::isMenuTreeViewActive(['supplier_search', 'event.index', 'restaurant.index', 'hotel.index', 'guide.index', 'flights.index', 'cruises.index'], $route))
                    menu-open
                    @endif
                    "
                @if (\App\Helper\DashboardHelper::isMenuTreeViewActive(['supplier_search', 'event.index', 'restaurant.index', 'hotel.index', 'guide.index','flights.index', 'cruises.index'], $route))
                style="display: block"
                    @endif
            >
                @if(Auth::user()->can('supplier_search'))
                    <li class="{{\App\Helper\DashboardHelper::isMenuActive('supplier_search', $route)}}">
                        <a href="{{ route('supplier_search') }}"><i class="fa fa-search"></i><span>{{trans('main.SupplierSearch')}}</span></a>
                    </li>
                @endif
                @if(Auth::user()->can('event.index'))
                    <li class="{!! \App\Helper\DashboardHelper::isMenuActive('event.index', $route) !!}"><a href="{{ route('event.index') }}"><i class='fa fa-map-signs'></i> <span>{{ trans('main.Events') }}</span></a></li>
                @endif
                @if(Auth::user()->can('restaurant.index'))
                    <li class="{!! \App\Helper\DashboardHelper::isMenuActive('restaurant.index', $route) !!}"><a href="{{ route('restaurant.index') }}"><i class='fa fa-coffee'></i> <span>{{ trans('main.Restaurants') }}</span></a></li>
                @endif
                @if(Auth::user()->can('hotel.index'))
                    <li class="{!! \App\Helper\DashboardHelper::isMenuActive('hotel.index', $route) !!}"><a href="{{ route('hotel.index') }}"><i class='fa fa-hotel'></i> <span>{{ trans('main.Hotels') }}</span></a></li>
                @endif
                @if(Auth::user()->can('guide.index'))
                    <li class="{!! \App\Helper\DashboardHelper::isMenuActive('guide.index', $route) !!}"><a href="{{ route('guide.index') }}"><i class='fa fa-street-view'></i> <span>{{ trans('main.Guides') }}</span></a></li>
                @endif
                {{--@if(Auth::user()->can('flights.index'))
                    <li class="{!! \App\Helper\DashboardHelper::isMenuActive('flights.index', $route) !!}"><a href="{{route('flights.index')}}"><i class="fa fa-plane"></i><span>{{trans('main.Flights')}}</span></a></li>
                @endif
                @if(Auth::user()->can('cruises.index'))
                    <li class="{!! \App\Helper\DashboardHelper::isMenuActive('cruises.index', $route) !!}"><a href="{{route('cruises.index')}}"><i class="fa fa-ship"></i><span>{{trans('main.Cruises')}}</span></a></li>
                @endif--}}
                @if(Auth::user()->can('cruises.index'))
                    <li class="{!! \App\Helper\DashboardHelper::isMenuActive('images.index', $route) !!}"><a href="{{route('images.index')}}"><i class="fa fa-image"></i><span>{{trans('main.Images')}}</span></a></li>
                @endif
            </ul>
        </li>

        <li class="treeview">
            <a href="#"><i class="fa fa-angle-left pull-right"></i><i class='fa fa-exchange'></i> <span>{{ trans('main.BusCompany') }}</span> </a>
            <ul class="treeview-menu
                @if (\App\Helper\DashboardHelper::isMenuTreeViewActive(['bus_calendar', 'transfer.index', 'currency_rate.index', 'bus.index', 'driver.index'], $route))menu-open @endif"
                @if (\App\Helper\DashboardHelper::isMenuTreeViewActive(['bus_calendar', 'transfer.index', 'currency_rate.index', 'bus.index', 'driver.index'], $route))style="display: block"@endif>

                @if(Auth::user()->can('bus_calendar'))
                <li class="{!! \App\Helper\DashboardHelper::isMenuActive('bus_calendar', $route) !!}">
                    <a href="{{route('bus_calendar')}}">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                        <span>{{trans('Buses')}} {{trans('main.Calendar')}}</span></a>
                </li>
                @endif
                @if(Auth::user()->can('transfer.index'))
                    <li class="{!! \App\Helper\DashboardHelper::isMenuActive('transfer.index', $route) !!}"><a href="{{ route('transfer.index') }}"><i class='fa fa-exchange'></i> <span>{{ trans('main.BusCompany') }}</span></a></li>
                @endif
                @if(Auth::user()->can('bus.index'))
                <li class="{!! \App\Helper\DashboardHelper::isMenuActive('bus.index', $route) !!}">
                    <a href="{{route('bus.index')}}">
                        <i class="fa fa-list" aria-hidden="true"></i>
                        <span>{{trans('Buses')}}</span></a>
                </li>
                @endif
                @if(Auth::user()->can('driver.index'))
                    <li class="{!! \App\Helper\DashboardHelper::isMenuActive('driver.index', $route) !!}"><a href="{{route('driver.index')}}"><i class="fa fa-ship"></i><span>{{trans('main.Drivers')}}</span></a></li>
                @endif
            </ul>
        </li>

        <li class="treeview">
            <a href="#"><i class="fa fa-angle-left pull-right"></i><i class='fa fa-map-signs'></i> <span>{{ trans('main.BaseInput') }}</span> </a>
            <ul class="treeview-menu
                @if (\App\Helper\DashboardHelper::isMenuTreeViewActive(['status.index', 'room_types.index', 'rate.index', 'currency_rate.index', 'currencies.index', 'criteria.index',], $route))menu-open @endif"
                @if (\App\Helper\DashboardHelper::isMenuTreeViewActive(['status.index', 'room_types.index', 'rate.index', 'currency_rate.index', 'currencies.index', 'criteria.index',], $route))style="display: block"@endif>
                @if(Auth::user()->can('status.index'))
                    <li class="{!! \App\Helper\DashboardHelper::isMenuActive('status.index', $route) !!}"><a href="{{ route('status.index') }}"><i class='fa fa-map-signs'></i> <span>{{ trans('main.Statuses') }}</span></a></li>
                @endif
                @if(Auth::user()->can('room_types.index'))
                    <li class="{!! \App\Helper\DashboardHelper::isMenuActive('room_types.index', $route) !!}"><a href="{{ route('room_types.index') }}"><i class='fa fa-map-signs'></i> <span>{{ trans('main.RoomTypes') }}</span></a></li>
                @endif
                @if(Auth::user()->can('rate.index'))
                <li class="{!! \App\Helper\DashboardHelper::isMenuActive('rate.index', $route) !!}"><a href="{{ route('rate.index') }}"><i class='fa fa-map-signs'></i> <span>{{ trans('main.Rates') }}</span></a></li>
                @endif
                @if(Auth::user()->can('currency_rate.index'))
                    <li class="{!! \App\Helper\DashboardHelper::isMenuActive('currency_rate.index', $route) !!}"><a href="{{ route('currency_rate.index') }}"><i class='fa fa-map-signs'></i> <span>{{ trans('main.CurrencyRate') }}</span></a></li>
                @endif
                @if(Auth::user()->can('currencies.index'))
                <li class="{!! \App\Helper\DashboardHelper::isMenuActive('currencies.index', $route) !!}"><a href="{{ route('currencies.index') }}"><i class='fa fa-map-signs'></i> <span>{{ trans('main.Currencies') }}</span></a></li>
                @endif
                @if(Auth::user()->can('criteria.index'))
                <li class="{!! \App\Helper\DashboardHelper::isMenuActive('criteria.index', $route) !!}"><a href="{{ route('criteria.index') }}"><i class='fa fa-map-signs'></i> <span>{{ trans('main.Criteria') }}</span></a></li>
                @endif
            </ul>
        </li>

        <li class="treeview">
            <a href="#"><i class="fa fa-angle-left pull-right"></i><i class='fa fa-comments'></i> <span>{{ trans('main.Communications') }}</span> </a>
            <ul class="treeview-menu
                @if (\App\Helper\DashboardHelper::isMenuTreeViewActive(['chat.main', 'email.index', 'templates.index', 'comment.index'], $route))menu-open @endif"
                @if (\App\Helper\DashboardHelper::isMenuTreeViewActive(['chat.main', 'email.index', 'templates.index', 'comment.index'], $route))style="display: block"@endif>
                {{--@if(Auth::user()->can('chat.main'))
                <li class="{!! \App\Helper\DashboardHelper::isMenuActive('chat.main', $route) !!}">
                    <a href="{{ route('chat.main') }}"><i class='fa fa-comments'></i> <span>{{trans('main.Chats')}}</span></a>
                </li>
                @endif--}}
                @if(Auth::user()->can('email.main'))
                <li class="{!! \App\Helper\DashboardHelper::isMenuActive('email.index', $route) !!}">
                    <a href="{{route('email.index')}}">
                        <i class="fa fa-list" aria-hidden="true"></i>
                        <span>{{trans('main.Email')}} {{trans('main.Inbox')}}</span></a>
                </li>
                <li class="{!! \App\Helper\DashboardHelper::isMenuActive('templates.index', $route) !!}">
                    <a href="{{route('templates.index')}}">
                        <i class="fa fa-file-code-o" aria-hidden="true"></i>
                        <span>{{trans('main.Email')}} {{trans('main.Templates')}}</span></a>
                </li>
                @endif
                @if(Auth::user()->can('comment.index'))
                <li class="{!! \App\Helper\DashboardHelper::isMenuActive('comment.index', $route) !!}">
                    <a href="{{ route('comment.index') }}">
                        <i class='fa fa-comment'></i>
                        <span>{{trans('main.Comments')}}</span>
                    </a>
                </li>
                @endif
            </ul>
        </li>

        @if(Auth::user()->can('clients.index'))
        <li class="{!! \App\Helper\DashboardHelper::isMenuActive('clients.index', $route) !!}">
            <a href="{{ route('clients.index') }}">
                <i class="fa fa-handshake-o" aria-hidden="true"></i>
                <span>{{trans('main.Clients')}}</span>
            </a>
        </li>
        @endif
		<li class="">
            <a href="{{ route('accounting.index') }}">
                <i class="fa fa-cog" aria-hidden="true" style = "color:white"></i>
                <span>{{trans('Settings')}}</span>
            </a>
			<ul class = "treeview-menu">
				 <li class="">
            <a href="{{ route('activities_index') }}">
                <i class="fa fa-book" aria-hidden="true" style = "color:white"></i>
                <span>{{trans('Activities')}}</span>
            </a>
        		</li>
			</ul>
        </li>
		
		 <li class="treeview">
            <a href="#"><i class="fa fa-angle-left pull-right"></i><i class='fa fa-comments'></i> <span>{{ trans('Accounting') }}</span> </a>
            <ul class="treeview-menu
                @if (\App\Helper\DashboardHelper::isMenuTreeViewActive(['chat.main', 'email.index', 'templates.index', 'accounting.index'], $route))menu-open @endif"
                @if (\App\Helper\DashboardHelper::isMenuTreeViewActive(['chat.main', 'email.index', 'templates.index', 'accounting.index'], $route))style="display: block"@endif>
                @if(Auth::user()->can('accounting.index'))
                <li class="{!! \App\Helper\DashboardHelper::isMenuActive('chat.main', $route) !!}">
                    <a href="{{ route('accounting.index') }}"><i class='fa fa-comments'></i> <span>{{trans('Billing')}}</span></a>
                </li>
                @endif
				@if(Auth::user()->can('chat.main'))
                <li class="{!! \App\Helper\DashboardHelper::isMenuActive('chat.main', $route) !!}">
                    <a href="{{ route('office.index') }}"><i class='fa fa-comments'></i> <span>{{trans('Offices')}}</span></a>
                </li>
                @endif
				@if(Auth::user()->can('invoices.index'))
                <li class="{!! \App\Helper\DashboardHelper::isMenuActive('chat.main', $route) !!}">
                    <a href="{{ route('invoices.index') }}"><i class='fa fa-comments'></i> <span>{{trans('Invoices')}}</span></a>
                </li>
                @endif
				
				@if(Auth::user()->can('reporting.index'))
                <li class="{!! \App\Helper\DashboardHelper::isMenuActive('chat.main', $route) !!}">
                    <a href="{{ route('taxes.index') }}"><i class='fa fa-comments'></i> <span>{{trans('Taxes')}}</span></a>
                </li>
                @endif
				@if(Auth::user()->can('reporting.index'))
                <li class="{!! \App\Helper\DashboardHelper::isMenuActive('chat.main', $route) !!}">
                    <a href="{{ route('reporting.index') }}"><i class='fa fa-comments'></i> <span>{{trans('Reporting')}}</span></a>
                </li>
                @endif

            </ul>
        </li>

    </ul>
</section>