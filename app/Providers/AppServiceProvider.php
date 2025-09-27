<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Rate;
//use Laravel\Dusk\DuskServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        Schema::defaultStringLength(191);
		 if(!defined('STAPLER_NULL')) {
       define('STAPLER_NULL', null);
       }

       // Force HTTPS in production-like environments
       if (config('app.env') !== 'local' || request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
           URL::forceScheme('https');
       }
    }
    

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register Pusher service
        $this->app->singleton('pusher', function ($app) {
            $config = $app['config']['broadcasting']['connections']['pusher'];

            // Check if Pusher credentials are configured
            if (empty($config['key']) || empty($config['secret']) || empty($config['app_id'])) {
                // Return a mock pusher instance if credentials are not configured
                return new \App\Services\MockPusher();
            }

            return new \Pusher\Pusher(
                $config['key'],
                $config['secret'],
                $config['app_id'],
                $config['options']
            );
        });

        $this->app->bind('App\Repository\Contracts\HotelRepository', 'App\Repository\HotelRepository\EloquentHotelRepository');
        $this->app->bind('App\Repository\Contracts\GuideRepository', 'App\Repository\GuideRepository\EloquentGuideRepository');
        $this->app->bind('App\Repository\Contracts\FlightRepository', 'App\Repository\FlightRepository\EloquentFlightRepository');
        $this->app->bind('App\Repository\Contracts\CruiseRepository', 'App\Repository\CruiseRepository\EloquentCruiseRepository');
        $this->app->bind('App\Repository\Contracts\EventRepository', 'App\Repository\EventRepository\EloquentEventRepository');
        $this->app->bind('App\Repository\Contracts\TransferRepository', 'App\Repository\TransferRepository\EloquentTransferRepository');
        $this->app->bind('App\Repository\Contracts\RestaurantRepository', 'App\Repository\RestaurantRepository\EloquentRestaurantRepository');
        $this->app->bind('App\Repository\Contracts\ChatRepository', 'App\Repository\ChatRepository\EloquentChatRepository');
        $this->app->bind('App\Repository\Contracts\TourRepository', 'App\Repository\TourRepository\EloquentTourRepository');
        $this->app->bind('App\Repository\Contracts\TaskRepository', 'App\Repository\TaskRepository\EloquentTaskRepository');
        $this->app->bind('App\Repository\Contracts\ClientRepository', 'App\Repository\ClientRepository\EloquentClientRepository');
        $this->app->bind('App\Repository\Contracts\SettingsRepository', 'App\Repository\SettingRepository\EloquentSettingsRepository');
        $this->app->bind('App\Repository\Contracts\BusRepository', 'App\Repository\BusRepository\EloquentBusRepository');
        $this->app->bind('App\Repository\Contracts\DriverRepository', 'App\Repository\DriverRepository\EloquentDriverRepository');
        $this->app->bind('App\Repository\Contracts\TourPackageRepository', 'App\Repository\TourPackageRepository\EloquentTourPackageRepository');
        $this->app->bind('App\Repository\Contracts\PackageMenuRepository', 'App\Repository\TourPackageRepository\PackageMenuRepository');
        $this->app->bind('App\Repository\Contracts\EmailRepository', 'App\Repository\EmailRepository\EloquentEmailRepository');
//        if ($this->app->environment('local', 'testing')) {
//            $this->app->register(DuskServiceProvider::class);
//        }
    }
}
