<?php

namespace App\Console\Commands;

use App\Notification;
use App\Setting;
use App\Status;
use App\Tour;
use App\TourPackage;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TourPackageNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checktourpackage:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tours_id = [];
        $status_id = [];
        $status_service = Status::query()
            ->where('type', 'service_in_tour')
            ->where('name', 'Pending')->first();
        $status_hotel = Status::query()
            ->where('type', 'hotel')
            ->where('name', 'Pending')->first();

        $status_id[] = $status_service ? $status_service->id : '';
        $status_id[] = $status_hotel ? $status_hotel->id : '';

        $packages = TourPackage::query()
            ->whereIn('status', $status_id)
            ->get();

        $value_day = 15;
        $setting_day = Setting::query()->where('name', 'tour_service_notification')->first();
        if($setting_day){
            $value_day = $setting_day->value;
        }

        foreach ($packages as $package) {
            foreach ($package->tourDays as $day) {
                if (!$tour = Tour::find($day->tour)) continue;
                $date = new Carbon();
                $endDate = Carbon::now()->addDays($value_day);
                if ($between_date = Carbon::parse($tour->departure_date)->between($date, $endDate)){
                    array_push($tours_id, $day->tour);
                }
            }
        };
        $tours = Tour::whereIn('id', $tours_id)->get();
        foreach ($tours as $tour) {
            $checkAuthor = false;
            foreach ($tour->users as $user) {
                if ($user->id == $tour->author) $checkAuthor = true;
                $link = "/tour/{$tour->id}";
                $notification = Notification::query()->create(['content' => "Pending services at {$tour->name}", 'link' => $link]);
                $user->notifications()->attach($notification);
            }
            if (!$checkAuthor){
                $link = "/tour/{$tour->id}";
                $notification = Notification::query()->create(['content' => "Pending services at {$tour->name}", 'link' => $link]);
                $u = User::find($tour->author);
                $u->notifications()->attach($notification);
            }
        }
    }
}
