@php
    $route = \Route::currentRouteName();
@endphp

<!-- Sidebar -->
<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="{{url('home')}}">
                <span class="navbar-brand-text">TMS</span>
            </a>
        </h1>

        <div class="navbar-nav flex-row d-lg-none">
            <div class="nav-item d-none d-md-flex me-3">
                <div class="btn-list">
                    <!-- Notification dropdown for mobile -->
                </div>
            </div>
        </div>

        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <!-- Dashboard -->
                <li class="nav-item {{App\Helper\DashboardHelper::isMenuActive('dashboard_main', $route)}}">
                    <a class="nav-link" href="{{url('home')}}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-dashboard icon"></i>
                        </span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                <!-- User Administration -->
                @if(Auth::user()->hasRole('admin'))
                <li class="nav-item dropdown {{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['users.index', 'roles.index', 'permissions.index', 'setting.index'], $route)) ? 'active' : ''}}">
                    <a class="nav-link dropdown-toggle" href="#navbar-users" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                       aria-expanded="{{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['users.index', 'roles.index', 'permissions.index', 'setting.index'], $route)) ? 'true' : 'false'}}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-users icon"></i>
                        </span>
                        <span class="nav-link-title">{{ trans('main.Users') }}</span>
                    </a>
                    <div class="dropdown-menu {{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['users.index', 'roles.index', 'permissions.index', 'setting.index'], $route)) ? 'show' : ''}}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('users.index', $route) !!}" href="{{url('/users')}}">
                                    <i class="ti ti-users icon me-2"></i>{{ trans('main.Users') }}
                                </a>
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('roles.index', $route) !!}" href="{{url('/roles')}}">
                                    <i class="ti ti-user-plus icon me-2"></i>{{ trans('main.Roles') }}
                                </a>
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('permissions.index', $route) !!}" href="{{url('/permissions')}}">
                                    <i class="ti ti-key icon me-2"></i>{{ trans('main.Permissions') }}
                                </a>
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('setting.index', $route) !!}" href="{{route('settings.index')}}">
                                    <i class="ti ti-settings icon me-2"></i>{{ trans('Settings') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                @endif

                <!-- Divider -->
                <li class="nav-item">
                    <div class="hr-text">{{ trans('main.TourManagement') }}</div>
                </li>

                <!-- Tours -->
                @if(Auth::user()->can('tour.index'))
                <li class="nav-item {!! \App\Helper\DashboardHelper::isMenuActive('tour.index',$route) !!}">
                    <a class="nav-link" href="{{route('tour.index')}}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-briefcase icon"></i>
                        </span>
                        <span class="nav-link-title">{{ trans('main.Tours') }}</span>
                    </a>
                </li>
                @endif

                <!-- Offers -->
                @if(Auth::user()->can('quotation.index'))
                <li class="nav-item dropdown {{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['current_offers.index', 'past_offers.index', 'current_bookings.index', 'cancellation_policies.index'], $route)) ? 'active' : ''}}">
                    <a class="nav-link dropdown-toggle" href="#navbar-offers" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                       aria-expanded="{{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['current_offers.index', 'past_offers.index', 'current_bookings.index', 'cancellation_policies.index'], $route)) ? 'true' : 'false'}}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-map-pin icon"></i>
                        </span>
                        <span class="nav-link-title">{{ trans('Offers') }}</span>
                    </a>
                    <div class="dropdown-menu {{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['current_offers.index', 'past_offers.index', 'current_bookings.index', 'cancellation_policies.index'], $route)) ? 'show' : ''}}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('current_offers.index',$route) !!}" href="{{route('current_offers.index')}}">
                                    <i class="ti ti-indent icon me-2"></i>{{ trans('Current Offers') }}
                                </a>
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('past_offers.index',$route) !!}" href="{{route('past_offers.index')}}">
                                    <i class="ti ti-indent icon me-2"></i>{{ trans('Past Offers') }}
                                </a>
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('current_bookings.index',$route) !!}" href="{{route('current_bookings.index')}}">
                                    <i class="ti ti-indent icon me-2"></i>{{ trans('Current Bookings') }}
                                </a>
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('cancellation_policies.index',$route) !!}" href="{{route('cancellation_policies.index')}}">
                                    <i class="ti ti-indent icon me-2"></i>{{ trans('Cancellation Policies') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                @endif

                <!-- Tasks -->
                @if(Auth::user()->can('task.index'))
                <li class="nav-item {!! \App\Helper\DashboardHelper::isMenuActive('task.index',$route) !!}">
                    <a class="nav-link" href="{{route('task.index')}}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-checkbox icon"></i>
                        </span>
                        <span class="nav-link-title">{{ trans('main.Tasks') }}</span>
                    </a>
                </li>
                @endif

                <!-- Services -->
                <li class="nav-item dropdown {{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['supplier_search', 'event.index', 'restaurant.index', 'hotel.index', 'guide.index', 'flights.index', 'cruises.index', 'images.index'], $route)) ? 'active' : ''}}">
                    <a class="nav-link dropdown-toggle" href="#navbar-services" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                       aria-expanded="{{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['supplier_search', 'event.index', 'restaurant.index', 'hotel.index', 'guide.index', 'flights.index', 'cruises.index', 'images.index'], $route)) ? 'true' : 'false'}}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-building icon"></i>
                        </span>
                        <span class="nav-link-title">{{ trans('main.Services') }}</span>
                    </a>
                    <div class="dropdown-menu {{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['supplier_search', 'event.index', 'restaurant.index', 'hotel.index', 'guide.index', 'flights.index', 'cruises.index', 'images.index'], $route)) ? 'show' : ''}}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @if(Auth::user()->can('supplier_search'))
                                <a class="dropdown-item {{\App\Helper\DashboardHelper::isMenuActive('supplier_search', $route)}}" href="{{ route('supplier_search') }}">
                                    <i class="ti ti-search icon me-2"></i>{{trans('main.SupplierSearch')}}
                                </a>
                                @endif
                                @if(Auth::user()->can('event.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('event.index', $route) !!}" href="{{ route('event.index') }}">
                                    <i class="ti ti-map-2 icon me-2"></i>{{ trans('main.Events') }}
                                </a>
                                @endif
                                @if(Auth::user()->can('restaurant.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('restaurant.index', $route) !!}" href="{{ route('restaurant.index') }}">
                                    <i class="ti ti-tools-kitchen icon me-2"></i>{{ trans('main.Restaurants') }}
                                </a>
                                @endif
                                @if(Auth::user()->can('hotel.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('hotel.index', $route) !!}" href="{{ route('hotel.index') }}">
                                    <i class="ti ti-building icon me-2"></i>{{ trans('main.Hotels') }}
                                </a>
                                @endif
                                @if(Auth::user()->can('guide.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('guide.index', $route) !!}" href="{{ route('guide.index') }}">
                                    <i class="ti ti-user-circle icon me-2"></i>{{ trans('main.Guides') }}
                                </a>
                                @endif
                                @if(Auth::user()->can('cruises.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('images.index', $route) !!}" href="{{route('images.index')}}">
                                    <i class="ti ti-photo icon me-2"></i>{{trans('main.Images')}}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Bus Company -->
                <li class="nav-item dropdown {{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['bus_calendar', 'transfer.index', 'bus.index', 'driver.index'], $route)) ? 'active' : ''}}">
                    <a class="nav-link dropdown-toggle" href="#navbar-bus" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                       aria-expanded="{{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['bus_calendar', 'transfer.index', 'bus.index', 'driver.index'], $route)) ? 'true' : 'false'}}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-bus icon"></i>
                        </span>
                        <span class="nav-link-title">{{ trans('main.BusCompany') }}</span>
                    </a>
                    <div class="dropdown-menu {{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['bus_calendar', 'transfer.index', 'bus.index', 'driver.index'], $route)) ? 'show' : ''}}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @if(Auth::user()->can('bus_calendar'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('bus_calendar', $route) !!}" href="{{route('bus_calendar')}}">
                                    <i class="ti ti-calendar icon me-2"></i>{{trans('Buses')}} {{trans('main.Calendar')}}
                                </a>
                                @endif
                                @if(Auth::user()->can('transfer.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('transfer.index', $route) !!}" href="{{ route('transfer.index') }}">
                                    <i class="ti ti-arrows-exchange icon me-2"></i>{{ trans('main.BusCompany') }}
                                </a>
                                @endif
                                @if(Auth::user()->can('bus.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('bus.index', $route) !!}" href="{{route('bus.index')}}">
                                    <i class="ti ti-list icon me-2"></i>{{trans('Buses')}}
                                </a>
                                @endif
                                @if(Auth::user()->can('driver.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('driver.index', $route) !!}" href="{{route('driver.index')}}">
                                    <i class="ti ti-steering-wheel icon me-2"></i>{{trans('main.Drivers')}}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Base Input -->
                <li class="nav-item dropdown {{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['status.index', 'room_types.index', 'rate.index', 'currency_rate.index', 'currencies.index', 'criteria.index'], $route)) ? 'active' : ''}}">
                    <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                       aria-expanded="{{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['status.index', 'room_types.index', 'rate.index', 'currency_rate.index', 'currencies.index', 'criteria.index'], $route)) ? 'true' : 'false'}}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-database icon"></i>
                        </span>
                        <span class="nav-link-title">{{ trans('main.BaseInput') }}</span>
                    </a>
                    <div class="dropdown-menu {{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['status.index', 'room_types.index', 'rate.index', 'currency_rate.index', 'currencies.index', 'criteria.index'], $route)) ? 'show' : ''}}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @if(Auth::user()->can('status.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('status.index', $route) !!}" href="{{ route('status.index') }}">
                                    <i class="ti ti-flag icon me-2"></i>{{ trans('main.Statuses') }}
                                </a>
                                @endif
                                @if(Auth::user()->can('room_types.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('room_types.index', $route) !!}" href="{{ route('room_types.index') }}">
                                    <i class="ti ti-bed icon me-2"></i>{{ trans('main.RoomTypes') }}
                                </a>
                                @endif
                                @if(Auth::user()->can('rate.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('rate.index', $route) !!}" href="{{ route('rate.index') }}">
                                    <i class="ti ti-star icon me-2"></i>{{ trans('main.Rates') }}
                                </a>
                                @endif
                                @if(Auth::user()->can('currency_rate.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('currency_rate.index', $route) !!}" href="{{ route('currency_rate.index') }}">
                                    <i class="ti ti-currency-dollar icon me-2"></i>{{ trans('main.CurrencyRate') }}
                                </a>
                                @endif
                                @if(Auth::user()->can('currencies.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('currencies.index', $route) !!}" href="{{ route('currencies.index') }}">
                                    <i class="ti ti-coins icon me-2"></i>{{ trans('main.Currencies') }}
                                </a>
                                @endif
                                @if(Auth::user()->can('criteria.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('criteria.index', $route) !!}" href="{{ route('criteria.index') }}">
                                    <i class="ti ti-list-check icon me-2"></i>{{ trans('main.Criteria') }}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Communications -->
                <li class="nav-item dropdown {{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['chat.main', 'email.index', 'templates.index', 'comment.index', 'snappymail.sso'], $route)) ? 'active' : ''}}">
                    <a class="nav-link dropdown-toggle" href="#navbar-communications" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                       aria-expanded="{{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['chat.main', 'email.index', 'templates.index', 'comment.index', 'snappymail.sso'], $route)) ? 'true' : 'false'}}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-message-circle icon"></i>
                        </span>
                        <span class="nav-link-title">{{ trans('main.Communications') }}</span>
                    </a>
                    <div class="dropdown-menu {{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['chat.main', 'email.index', 'templates.index', 'comment.index', 'snappymail.sso'], $route)) ? 'show' : ''}}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @if(Auth::user()->can('email.main'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('email.index', $route) !!}" href="{{route('email.index')}}">
                                    <i class="ti ti-mail icon me-2"></i>{{trans('main.Email')}} {{trans('main.Inbox')}}
                                </a>
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('templates.index', $route) !!}" href="{{route('templates.index')}}">
                                    <i class="ti ti-template icon me-2"></i>{{trans('main.Email')}} {{trans('main.Templates')}}
                                </a>
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('snappymail.sso', $route) !!}" href="{{route('snappymail.sso')}}" target="_blank">
                                    <i class="ti ti-mail-opened icon me-2"></i>Webmail (SnappyMail)
                                </a>
                                @endif
                                @if(Auth::user()->can('comment.index'))
                                <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('comment.index', $route) !!}" href="{{ route('comment.index') }}">
                                    <i class="ti ti-message icon me-2"></i>{{trans('main.Comments')}}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Clients -->
                @if(Auth::user()->can('clients.index'))
                <li class="nav-item {!! \App\Helper\DashboardHelper::isMenuActive('clients.index', $route) !!}">
                    <a class="nav-link" href="{{ route('clients.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-users icon"></i>
                        </span>
                        <span class="nav-link-title">{{trans('main.Clients')}}</span>
                    </a>
                </li>
                @endif

                <!-- Accounting -->
                <li class="nav-item dropdown {{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['accounting.index', 'office.index', 'invoices.index', 'taxes.index', 'reporting.index'], $route)) ? 'active' : ''}}">
                    <a class="nav-link dropdown-toggle" href="#navbar-accounting" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                       aria-expanded="{{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['accounting.index', 'office.index', 'invoices.index', 'taxes.index', 'reporting.index'], $route)) ? 'true' : 'false'}}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-calculator icon"></i>
                        </span>
                        <span class="nav-link-title">{{ trans('Accounting') }}</span>
                    </a>
                    <div class="dropdown-menu {{(\App\Helper\DashboardHelper::isMenuTreeViewActive(['accounting.index', 'office.index', 'invoices.index', 'taxes.index', 'reporting.index'], $route)) ? 'show' : ''}}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @if(Auth::user()->can('accounting.index'))
                                <a class="dropdown-item" href="{{ route('accounting.index') }}">
                                    <i class="ti ti-coin icon me-2"></i>{{trans('Billing')}}
                                </a>
                                @endif
                                @if(Auth::user()->can('chat.main'))
                                <a class="dropdown-item" href="{{ route('office.index') }}">
                                    <i class="ti ti-building-bank icon me-2"></i>{{trans('Offices')}}
                                </a>
                                @endif
                                @if(Auth::user()->can('invoices.index'))
                                <a class="dropdown-item" href="{{ route('invoices.index') }}">
                                    <i class="ti ti-file-invoice icon me-2"></i>{{trans('Invoices')}}
                                </a>
                                @endif
                                @if(Auth::user()->can('reporting.index'))
                                <a class="dropdown-item" href="{{ route('taxes.index') }}">
                                    <i class="ti ti-receipt-tax icon me-2"></i>{{trans('Taxes')}}
                                </a>
                                <a class="dropdown-item" href="{{ route('reporting.index') }}">
                                    <i class="ti ti-report icon me-2"></i>{{trans('Reporting')}}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Activities/Settings -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('activities_index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-activity icon"></i>
                        </span>
                        <span class="nav-link-title">{{trans('Activities')}}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
