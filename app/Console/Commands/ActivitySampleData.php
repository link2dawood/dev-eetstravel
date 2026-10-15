<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Sample entries for the Activities page: tour, service, bus, driver, comment and
 * template events over the last month, by several users and "System", with View
 * links to records that exist here. Every description starts with "QA Sample";
 * the ids are kept in storage/app/sample-data/activities.json so `--remove`
 * deletes exactly these entries and nothing from the real audit trail.
 */
class ActivitySampleData extends Command
{
    protected $signature = 'activities:sample-data {--remove : Delete the sample entries instead of creating them}';

    protected $description = 'Create (or remove) QA Sample activity log entries';

    public function handle()
    {
        return $this->option('remove') ? $this->remove() : $this->create();
    }

    private function manifestPath()
    {
        return storage_path('app/sample-data/activities.json');
    }

    private function create()
    {
        if (is_file($this->manifestPath())) {
            $this->error('Activity sample data already exists. Run with --remove first.');
            return 1;
        }

        // users: the first few accounts, plus null = System
        $users = DB::table('users')->orderBy('id')->limit(4)->pluck('id')->all();
        $user = function ($i) use ($users) {
            return $i === null ? null : ($users[$i % max(count($users), 1)] ?? null);
        };

        // subjects that exist on this server (link only when found)
        $tour = DB::table('tours')->whereNull('deleted_at')->orderByDesc('id')->first(['id', 'name']);
        $package = $tour ? DB::table('tour_packages')->where('tour_id', $tour->id)->whereNull('deleted_at')->first(['id', 'name']) : null;
        $bus = DB::table('buses')->whereNull('deleted_at')->orderByDesc('id')->first(['id', 'name']);
        $driver = DB::table('drivers')->orderByDesc('id')->first(['id', 'name']);
        $transfer = DB::table('transfers')->whereNull('deleted_at')->orderByDesc('id')->first(['id', 'name']);
        $template = DB::table('templates')->orderByDesc('id')->first(['id', 'name', 'service_id']);

        $tourName = $tour->name ?? 'Danube Capitals';
        $entries = [
            // [days ago, time, user index (null = System), log name, subject class, subject, action, description, link]
            [29, '09:12', 0, 'Tour', 'App\Tour', $tour, 'created', "Tour: {$tourName} created", $tour ? "/tour/{$tour->id}" : null],
            [28, '10:40', 1, 'TourPackage', 'App\TourPackage', $package, 'created', 'TourPackage: Hotel Prague (3 nights) added to ' . $tourName, $tour ? "/tour/{$tour->id}" : null],
            [27, '14:05', 1, 'TourPackage', 'App\TourPackage', $package, 'updated', "change value of 'pax' from '38' to '42' in Hotel Prague", $tour ? "/tour/{$tour->id}" : null],
            [25, '08:30', 2, 'Transfer', 'App\Transfer', $transfer, 'created', 'Bus Company: ' . ($transfer->name ?? 'Prague Coaches') . ' created', $transfer ? "/transfer/{$transfer->id}" : null],
            [24, '11:15', 2, 'Bus', 'App\Bus', $bus, 'created', 'Bus: ' . ($bus->name ?? 'Setra S 516 HD') . ' created', $bus ? "/bus/{$bus->id}" : null],
            [24, '11:20', 2, 'Driver', 'App\Driver', $driver, 'created', 'Driver: ' . ($driver->name ?? 'Petr Svoboda') . ' created', $driver ? "/driver/{$driver->id}" : null],
            [21, '16:45', 0, 'Tour', 'App\Tour', $tour, 'updated', "change value of 'status' from 'Pending' to 'Confirmed' in {$tourName}", $tour ? "/tour/{$tour->id}" : null],
            [20, '09:00', null, 'BusDay', 'App\BusDay', null, 'created', 'BusDay: trip days generated for ' . $tourName, null],
            [18, '13:30', 3, 'Comment', 'App\Comment', null, 'created', 'Comment: Client asked for an earlier pick-up at the airport created at ' . $tourName . ' (Tour)', $tour ? "/tour/{$tour->id}" : null],
            [17, '10:10', 1, 'TourPackage', 'App\TourPackage', $package, 'updated', "change value of 'status' from 'Requested' to 'Confirmed' in Restaurant dinner", $tour ? "/tour/{$tour->id}" : null],
            [15, '15:00', 0, 'Templates', 'App\Templates', $template, 'updated', 'Templates: ' . ($template->name ?? 'Hotel Booking Request') . ' updated', $template ? "/templates/{$template->service_id}" : null],
            [12, '09:45', 2, 'Bus', 'App\Bus', $bus, 'updated', "change value of 'bus_number' from '1AB 2344' to '1AB 2345' in " . ($bus->name ?? 'Setra S 516 HD'), $bus ? "/bus/{$bus->id}" : null],
            [10, '12:00', 3, 'TourPackage', 'App\TourPackage', $package, 'deleted', 'TourPackage: Museum tickets deleted from ' . $tourName, null],
            [8, '17:20', 1, 'Comment', 'App\Comment', null, 'created', 'Comment: Rooming list sent to the hotel created at ' . $tourName . ' (Tour)', $tour ? "/tour/{$tour->id}" : null],
            [6, '08:05', null, 'Tour', 'App\Tour', $tour, 'updated', "change value of 'pax' from '42' to '44' in {$tourName} (import)", $tour ? "/tour/{$tour->id}" : null],
            [4, '11:35', 0, 'Driver', 'App\Driver', $driver, 'updated', "change value of 'phone' in " . ($driver->name ?? 'Petr Svoboda'), $driver ? "/driver/{$driver->id}" : null],
            [3, '14:50', 2, 'Transfer', 'App\Transfer', $transfer, 'updated', "change value of 'work_phone' in " . ($transfer->name ?? 'Prague Coaches'), $transfer ? "/transfer/{$transfer->id}" : null],
            [2, '09:25', 3, 'TourPackage', 'App\TourPackage', $package, 'created', 'TourPackage: Coach transfer airport - hotel added to ' . $tourName, $tour ? "/tour/{$tour->id}" : null],
            [1, '10:00', 1, 'Templates', 'App\Templates', $template, 'created', 'Templates: Restaurant Group Reservation created for service Restaurant', $template ? "/templates/{$template->service_id}" : null],
            [0, '08:15', 0, 'Tour', 'App\Tour', $tour, 'updated', "change value of 'retirement_date' in {$tourName}", $tour ? "/tour/{$tour->id}" : null],
        ];

        $ids = DB::transaction(function () use ($entries, $user) {
            $ids = [];
            foreach ($entries as [$daysAgo, $time, $userIndex, $logName, $class, $subject, $action, $text, $link]) {
                $at = Carbon::today()->subDays($daysAgo)->setTimeFromTimeString($time);
                $causer = $user($userIndex);
                $ids[] = DB::table('activity_log')->insertGetId([
                    'log_name' => $logName,
                    'description' => 'QA Sample ' . $text,
                    'subject_id' => $subject->id ?? null,
                    'subject_type' => $class,
                    'causer_id' => $causer,
                    'causer_type' => $causer ? 'App\User' : null,
                    'properties' => json_encode(['action' => $action, 'link' => $link ? url($link) : '']),
                    'created_at' => $at,
                    'updated_at' => $at,
                ]);
            }
            return $ids;
        });

        if (!is_dir(dirname($this->manifestPath()))) {
            mkdir(dirname($this->manifestPath()), 0775, true);
        }
        file_put_contents($this->manifestPath(), json_encode(['activity_log' => $ids], JSON_PRETTY_PRINT));

        $this->info(count($ids) . ' QA Sample activity entries created (last 30 days). Remove them with: php artisan activities:sample-data --remove');

        return 0;
    }

    private function remove()
    {
        if (!is_file($this->manifestPath())) {
            $this->error('No activity sample data found (' . $this->manifestPath() . ').');
            return 1;
        }

        $ids = json_decode(file_get_contents($this->manifestPath()), true)['activity_log'] ?? [];
        $deleted = DB::table('activity_log')->whereIn('id', $ids)->where('description', 'like', 'QA Sample %')->delete();
        unlink($this->manifestPath());
        $this->info("{$deleted} QA Sample activity entries removed.");

        return 0;
    }
}
