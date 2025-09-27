<section class="sidebar">
    <?php
        $route = \Route::currentRouteName();
    ?>
    <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <li class="treeview <?php echo e(App\Helper\DashboardHelper::isMenuActive('dashboard_main', $route)); ?>">
            <a href="<?php echo e(url('home')); ?>">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span></i>
            </a>
        </li>
        <li class="header"><?php echo e(trans('main.Usersadministration')); ?></li>

        <?php if(Auth::user()->hasRole('admin')): ?>
        <li class="treeview">
            <a href="#"><i class="fa fa-angle-left pull-right"></i><i class='fa fa-users'></i> <span><?php echo e(trans('main.Users')); ?></span> </a>
            <ul class="treeview-menu
                    <?php if(\App\Helper\DashboardHelper::isMenuTreeViewActive(['users.index', 'roles.index', 'permissions.index', 'setting.index'], $route)): ?>
                    menu-open
                    <?php endif; ?>
            "
                <?php if(\App\Helper\DashboardHelper::isMenuTreeViewActive(['users.index', 'roles.index', 'permissions.index', 'setting.index'], $route)): ?>
                style="display: block"
                    <?php endif; ?>
            >
                <li class="treeview <?php echo \App\Helper\DashboardHelper::isMenuActive('users.index', $route); ?>"><a href="<?php echo e(url('/users')); ?>"><i class="fa fa-users"></i> <span><?php echo e(trans('main.Users')); ?></span></a></li>
                <li class="treeview <?php echo \App\Helper\DashboardHelper::isMenuActive('roles.index', $route); ?>"><a href="<?php echo e(url('/roles')); ?>"><i class="fa fa-user-plus"></i> <span><?php echo e(trans('main.Roles')); ?></span></a></li>
                <li class="treeview <?php echo \App\Helper\DashboardHelper::isMenuActive('permissions.index', $route); ?>"><a href="<?php echo e(url('/permissions')); ?>"><i class="fa fa-key"></i> <span><?php echo e(trans('main.Permissions')); ?></span></a></li>
                <li class="<?php echo e(App\Helper\DashboardHelper::isMenuActive('setting.index',$route)); ?>">
                            <a href="<?php echo e(route('settings.index')); ?>">
                                <i class="fa fa-cog" aria-hidden='true'></i>
                                <span><?php echo e(trans('Settings')); ?></span>
                            </a>
                        </li>
            </ul>
        </li>
        <?php endif; ?>

        <li class="header"><?php echo e(trans('main.TourManagement')); ?></li>

       

		 <?php if(Auth::user()->can('tour.index')): ?>
            <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('tour.index',$route); ?>"><a href="<?php echo e(route('tour.index')); ?>"><i class='fa fa-suitcase'></i> <span><?php echo e(trans('main.Tours')); ?></span></a></li>
        <?php endif; ?>

        <?php if(Auth::user()->can('quotation.index')): ?>
			 <li class="treeview">
				 <a href="#"><i class="fa fa-angle-left pull-right"></i><i class='fa fa-map-pin'></i> <span><?php echo e(trans('Offers')); ?></span> </a>
				 <ul class="treeview-menu">			
            <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('current_offers.index',$route); ?>"><a href="<?php echo e(route('current_offers.index')); ?>"><i class='fa fa-indent'></i> <span><?php echo e(trans('Current Offers')); ?></span></a></li>
					  <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('past_offers.index',$route); ?>"><a href="<?php echo e(route('past_offers.index')); ?>"><i class='fa fa-indent'></i> <span><?php echo e(trans('Past Offers')); ?></span></a></li>
					 <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('current_bookings.index',$route); ?>"><a href="<?php echo e(route('current_bookings.index')); ?>"><i class='fa fa-indent'></i> <span><?php echo e(trans('Current Bookings')); ?></span></a></li>
					 <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('cancellation_policies.index',$route); ?>"><a href="<?php echo e(route('cancellation_policies.index')); ?>"><i class='fa fa-indent'></i> <span><?php echo e(trans('Cancellation Policies')); ?></span></a></li>
				 </ul>
			</li>
        <?php endif; ?>

        <?php if(Auth::user()->can('task.index')): ?>
        <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('task.index',$route); ?>">
            <a href="<?php echo e(route('task.index')); ?>"><i class='fa fa-tasks'></i> <span><?php echo e(trans('main.Tasks')); ?></span></a></li>
        <?php endif; ?>

        <li class="treeview">
            <a href="#"><i class="fa fa-angle-left pull-right"></i><i class='fa fa-map-pin'></i> <span><?php echo e(trans('main.Services')); ?></span> </a>
            <ul class="treeview-menu
                    <?php if(\App\Helper\DashboardHelper::isMenuTreeViewActive(['supplier_search', 'event.index', 'restaurant.index', 'hotel.index', 'guide.index', 'flights.index', 'cruises.index'], $route)): ?>
                    menu-open
                    <?php endif; ?>
                    "
                <?php if(\App\Helper\DashboardHelper::isMenuTreeViewActive(['supplier_search', 'event.index', 'restaurant.index', 'hotel.index', 'guide.index','flights.index', 'cruises.index'], $route)): ?>
                style="display: block"
                    <?php endif; ?>
            >
                <?php if(Auth::user()->can('supplier_search')): ?>
                    <li class="<?php echo e(\App\Helper\DashboardHelper::isMenuActive('supplier_search', $route)); ?>">
                        <a href="<?php echo e(route('supplier_search')); ?>"><i class="fa fa-search"></i><span><?php echo e(trans('main.SupplierSearch')); ?></span></a>
                    </li>
                <?php endif; ?>
                <?php if(Auth::user()->can('event.index')): ?>
                    <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('event.index', $route); ?>"><a href="<?php echo e(route('event.index')); ?>"><i class='fa fa-map-signs'></i> <span><?php echo e(trans('main.Events')); ?></span></a></li>
                <?php endif; ?>
                <?php if(Auth::user()->can('restaurant.index')): ?>
                    <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('restaurant.index', $route); ?>"><a href="<?php echo e(route('restaurant.index')); ?>"><i class='fa fa-coffee'></i> <span><?php echo e(trans('main.Restaurants')); ?></span></a></li>
                <?php endif; ?>
                <?php if(Auth::user()->can('hotel.index')): ?>
                    <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('hotel.index', $route); ?>"><a href="<?php echo e(route('hotel.index')); ?>"><i class='fa fa-hotel'></i> <span><?php echo e(trans('main.Hotels')); ?></span></a></li>
                <?php endif; ?>
                <?php if(Auth::user()->can('guide.index')): ?>
                    <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('guide.index', $route); ?>"><a href="<?php echo e(route('guide.index')); ?>"><i class='fa fa-street-view'></i> <span><?php echo e(trans('main.Guides')); ?></span></a></li>
                <?php endif; ?>
                
                <?php if(Auth::user()->can('cruises.index')): ?>
                    <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('images.index', $route); ?>"><a href="<?php echo e(route('images.index')); ?>"><i class="fa fa-image"></i><span><?php echo e(trans('main.Images')); ?></span></a></li>
                <?php endif; ?>
            </ul>
        </li>

        <li class="treeview">
            <a href="#"><i class="fa fa-angle-left pull-right"></i><i class='fa fa-exchange'></i> <span><?php echo e(trans('main.BusCompany')); ?></span> </a>
            <ul class="treeview-menu
                <?php if(\App\Helper\DashboardHelper::isMenuTreeViewActive(['bus_calendar', 'transfer.index', 'currency_rate.index', 'bus.index', 'driver.index'], $route)): ?>menu-open <?php endif; ?>"
                <?php if(\App\Helper\DashboardHelper::isMenuTreeViewActive(['bus_calendar', 'transfer.index', 'currency_rate.index', 'bus.index', 'driver.index'], $route)): ?>style="display: block"<?php endif; ?>>

                <?php if(Auth::user()->can('bus_calendar')): ?>
                <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('bus_calendar', $route); ?>">
                    <a href="<?php echo e(route('bus_calendar')); ?>">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                        <span><?php echo e(trans('Buses')); ?> <?php echo e(trans('main.Calendar')); ?></span></a>
                </li>
                <?php endif; ?>
                <?php if(Auth::user()->can('transfer.index')): ?>
                    <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('transfer.index', $route); ?>"><a href="<?php echo e(route('transfer.index')); ?>"><i class='fa fa-exchange'></i> <span><?php echo e(trans('main.BusCompany')); ?></span></a></li>
                <?php endif; ?>
                <?php if(Auth::user()->can('bus.index')): ?>
                <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('bus.index', $route); ?>">
                    <a href="<?php echo e(route('bus.index')); ?>">
                        <i class="fa fa-list" aria-hidden="true"></i>
                        <span><?php echo e(trans('Buses')); ?></span></a>
                </li>
                <?php endif; ?>
                <?php if(Auth::user()->can('driver.index')): ?>
                    <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('driver.index', $route); ?>"><a href="<?php echo e(route('driver.index')); ?>"><i class="fa fa-ship"></i><span><?php echo e(trans('main.Drivers')); ?></span></a></li>
                <?php endif; ?>
            </ul>
        </li>

        <li class="treeview">
            <a href="#"><i class="fa fa-angle-left pull-right"></i><i class='fa fa-map-signs'></i> <span><?php echo e(trans('main.BaseInput')); ?></span> </a>
            <ul class="treeview-menu
                <?php if(\App\Helper\DashboardHelper::isMenuTreeViewActive(['status.index', 'room_types.index', 'rate.index', 'currency_rate.index', 'currencies.index', 'criteria.index',], $route)): ?>menu-open <?php endif; ?>"
                <?php if(\App\Helper\DashboardHelper::isMenuTreeViewActive(['status.index', 'room_types.index', 'rate.index', 'currency_rate.index', 'currencies.index', 'criteria.index',], $route)): ?>style="display: block"<?php endif; ?>>
                <?php if(Auth::user()->can('status.index')): ?>
                    <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('status.index', $route); ?>"><a href="<?php echo e(route('status.index')); ?>"><i class='fa fa-map-signs'></i> <span><?php echo e(trans('main.Statuses')); ?></span></a></li>
                <?php endif; ?>
                <?php if(Auth::user()->can('room_types.index')): ?>
                    <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('room_types.index', $route); ?>"><a href="<?php echo e(route('room_types.index')); ?>"><i class='fa fa-map-signs'></i> <span><?php echo e(trans('main.RoomTypes')); ?></span></a></li>
                <?php endif; ?>
                <?php if(Auth::user()->can('rate.index')): ?>
                <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('rate.index', $route); ?>"><a href="<?php echo e(route('rate.index')); ?>"><i class='fa fa-map-signs'></i> <span><?php echo e(trans('main.Rates')); ?></span></a></li>
                <?php endif; ?>
                <?php if(Auth::user()->can('currency_rate.index')): ?>
                    <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('currency_rate.index', $route); ?>"><a href="<?php echo e(route('currency_rate.index')); ?>"><i class='fa fa-map-signs'></i> <span><?php echo e(trans('main.CurrencyRate')); ?></span></a></li>
                <?php endif; ?>
                <?php if(Auth::user()->can('currencies.index')): ?>
                <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('currencies.index', $route); ?>"><a href="<?php echo e(route('currencies.index')); ?>"><i class='fa fa-map-signs'></i> <span><?php echo e(trans('main.Currencies')); ?></span></a></li>
                <?php endif; ?>
                <?php if(Auth::user()->can('criteria.index')): ?>
                <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('criteria.index', $route); ?>"><a href="<?php echo e(route('criteria.index')); ?>"><i class='fa fa-map-signs'></i> <span><?php echo e(trans('main.Criteria')); ?></span></a></li>
                <?php endif; ?>
            </ul>
        </li>

        <li class="treeview">
            <a href="#"><i class="fa fa-angle-left pull-right"></i><i class='fa fa-comments'></i> <span><?php echo e(trans('main.Communications')); ?></span> </a>
            <ul class="treeview-menu
                <?php if(\App\Helper\DashboardHelper::isMenuTreeViewActive(['chat.main', 'email.index', 'templates.index', 'comment.index'], $route)): ?>menu-open <?php endif; ?>"
                <?php if(\App\Helper\DashboardHelper::isMenuTreeViewActive(['chat.main', 'email.index', 'templates.index', 'comment.index'], $route)): ?>style="display: block"<?php endif; ?>>
                
                <?php if(Auth::user()->can('email.main')): ?>
                <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('email.index', $route); ?>">
                    <a href="<?php echo e(route('email.index')); ?>">
                        <i class="fa fa-list" aria-hidden="true"></i>
                        <span><?php echo e(trans('main.Email')); ?> <?php echo e(trans('main.Inbox')); ?></span></a>
                </li>
                <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('templates.index', $route); ?>">
                    <a href="<?php echo e(route('templates.index')); ?>">
                        <i class="fa fa-file-code-o" aria-hidden="true"></i>
                        <span><?php echo e(trans('main.Email')); ?> <?php echo e(trans('main.Templates')); ?></span></a>
                </li>
                <?php endif; ?>
                <?php if(Auth::user()->can('comment.index')): ?>
                <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('comment.index', $route); ?>">
                    <a href="<?php echo e(route('comment.index')); ?>">
                        <i class='fa fa-comment'></i>
                        <span><?php echo e(trans('main.Comments')); ?></span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </li>

        <?php if(Auth::user()->can('clients.index')): ?>
        <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('clients.index', $route); ?>">
            <a href="<?php echo e(route('clients.index')); ?>">
                <i class="fa fa-handshake-o" aria-hidden="true"></i>
                <span><?php echo e(trans('main.Clients')); ?></span>
            </a>
        </li>
        <?php endif; ?>
		<li class="">
            <a href="<?php echo e(route('accounting.index')); ?>">
                <i class="fa fa-cog" aria-hidden="true" style = "color:white"></i>
                <span><?php echo e(trans('Settings')); ?></span>
            </a>
			<ul class = "treeview-menu">
				 <li class="">
            <a href="<?php echo e(route('activities_index')); ?>">
                <i class="fa fa-book" aria-hidden="true" style = "color:white"></i>
                <span><?php echo e(trans('Activities')); ?></span>
            </a>
        		</li>
			</ul>
        </li>
		
		 <li class="treeview">
            <a href="#"><i class="fa fa-angle-left pull-right"></i><i class='fa fa-comments'></i> <span><?php echo e(trans('Accounting')); ?></span> </a>
            <ul class="treeview-menu
                <?php if(\App\Helper\DashboardHelper::isMenuTreeViewActive(['chat.main', 'email.index', 'templates.index', 'accounting.index'], $route)): ?>menu-open <?php endif; ?>"
                <?php if(\App\Helper\DashboardHelper::isMenuTreeViewActive(['chat.main', 'email.index', 'templates.index', 'accounting.index'], $route)): ?>style="display: block"<?php endif; ?>>
                <?php if(Auth::user()->can('accounting.index')): ?>
                <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('chat.main', $route); ?>">
                    <a href="<?php echo e(route('accounting.index')); ?>"><i class='fa fa-comments'></i> <span><?php echo e(trans('Billing')); ?></span></a>
                </li>
                <?php endif; ?>
				<?php if(Auth::user()->can('chat.main')): ?>
                <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('chat.main', $route); ?>">
                    <a href="<?php echo e(route('office.index')); ?>"><i class='fa fa-comments'></i> <span><?php echo e(trans('Offices')); ?></span></a>
                </li>
                <?php endif; ?>
				<?php if(Auth::user()->can('invoices.index')): ?>
                <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('chat.main', $route); ?>">
                    <a href="<?php echo e(route('invoices.index')); ?>"><i class='fa fa-comments'></i> <span><?php echo e(trans('Invoices')); ?></span></a>
                </li>
                <?php endif; ?>
				
				<?php if(Auth::user()->can('reporting.index')): ?>
                <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('chat.main', $route); ?>">
                    <a href="<?php echo e(route('taxes.index')); ?>"><i class='fa fa-comments'></i> <span><?php echo e(trans('Taxes')); ?></span></a>
                </li>
                <?php endif; ?>
				<?php if(Auth::user()->can('reporting.index')): ?>
                <li class="<?php echo \App\Helper\DashboardHelper::isMenuActive('chat.main', $route); ?>">
                    <a href="<?php echo e(route('reporting.index')); ?>"><i class='fa fa-comments'></i> <span><?php echo e(trans('Reporting')); ?></span></a>
                </li>
                <?php endif; ?>

            </ul>
        </li>

    </ul>
</section><?php /**PATH /var/www/html/resources/views/scaffold-interface/layouts/sidebar.blade.php ENDPATH**/ ?>