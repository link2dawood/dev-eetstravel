<?php

namespace App\Console\Commands;

use App\Bus;
use App\BusDay;
use App\Comment;
use App\Driver;
use App\Helper\BusDayHelper;
use App\Http\Controllers\TourPackageController;
use App\TourPackage;
use App\Transfer;
use App\Trip;
use App\TripToDrivers;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Sample data for the Bus Company menu (bus companies, buses, drivers and the
 * Buses Calendar). Everything is named "QA Sample ..." so it is easy to spot,
 * and `--remove` deletes exactly what this command created.
 *
 * Calendar dates are relative to today, so the samples are always visible in
 * the current month. Tour transfers are added only to real (non-quotation)
 * tours starting in the next 60 days, through the same controller the
 * calendar uses.
 */
class BusSampleData extends Command
{
    protected $signature = 'bus:sample-data {--remove : Delete the QA Sample records instead of creating them} {--standalone : Create standalone trips without linking to existing tours}';

    protected $description = 'Create (or remove) QA Sample bus companies, buses, drivers and calendar trips';

    const PREFIX = 'QA Sample ';

    // company => [country, city name, address, phone, contact, buses[name => plate], drivers[name => phone]]
    const COMPANIES = [
        'Prague Coaches' => ['CZ', 'Prague', 'Na Florenci 1410/3, 110 00 Prague', '+420 222 000 111', 'Jana Novakova',
            ['Setra S 516 HD' => '1AB 2345', 'Mercedes Tourismo' => '2CD 6789'],
            ['Petr Svoboda' => '+420 601 111 222', 'Tomas Dvorak' => '+420 602 333 444']],
        'Danube Coaches' => ['AT', 'Vienna', 'Mariahilfer Strasse 120, 1070 Vienna', '+43 1 523 0000', 'Lukas Gruber',
            ["MAN Lion's Coach" => 'W-12345B', 'Neoplan Cityliner' => 'W-67890C'],
            ['Stefan Huber' => '+43 664 111 2233', 'Markus Bauer' => '+43 664 444 5566']],
        'Budapest Bus Lines' => ['HU', 'Budapest', 'Andrassy ut 50, 1062 Budapest', '+36 1 300 0000', 'Eszter Nagy',
            ['Volvo 9700' => 'ABC-123', 'Irizar i6' => 'DEF-456'],
            ['Gabor Kovacs' => '+36 20 111 2222', 'Zoltan Szabo' => '+36 30 333 4444']],
    ];

    // [name, company, bus index, driver indexes, start offset, days, status name, comment]
    const TRIPS = [
        ['Prague City Tour', 'Prague Coaches', 0, [0], 2, 3, 'Confirmed', 'Pick-up at Hotel Paris, 08:30. Group of 42.'],
        ['Prague - Cesky Krumlov', 'Prague Coaches', 1, [1], 5, 3, 'Planned, need to check with coach co', null],
        ['Vienna Airport Shuttle', 'Danube Coaches', 0, [0], 1, 1, 'Confirmed', null],
        ['Wachau Valley Day Trip', 'Danube Coaches', 1, [1], 8, 2, 'Waiting for coach co. to confirm', null],
        ['Budapest - Lake Balaton', 'Budapest Bus Lines', 0, [0, 1], 3, 4, 'Coach change/repalcement/broke down', 'Replacement coach requested, original has a gearbox fault.'],
        ['Danube Bend Excursion', 'Budapest Bus Lines', 1, [1], 10, 2, 'Coach not available', null],
        ['Prague - Dresden', 'Prague Coaches', 0, [0], 20, 4, 'Planned, need to check with coach co', null],
        ['Salzburg Transfer', 'Danube Coaches', 0, [0], 30, 2, 'Confirmed', null],
    ];

    public function handle(BusDayHelper $busDayHelper)
    {
        return $this->option('remove') ? $this->remove() : $this->create($busDayHelper);
    }

    private function create(BusDayHelper $busDayHelper)
    {
        if (Transfer::query()->where('name', 'like', self::PREFIX . '%')->exists()) {
            $this->error('QA Sample data already exists. Run with --remove first.');
            return 1;
        }

        $statuses = DB::table('status')->where('type', 'bus')->pluck('id', 'name');
        $today = Carbon::today();

        $made = DB::transaction(function () use ($statuses, $today, $busDayHelper) {
            $made = ['companies' => 0, 'buses' => 0, 'drivers' => 0, 'trips' => 0, 'trip days' => 0, 'comments' => 0];
            $fleet = [];

            foreach (self::COMPANIES as $name => [$country, $cityName, $address, $phone, $contact, $buses, $drivers]) {
                $cityId = DB::table('cities')->where('country', $country)->where('name', $cityName)->value('id');
                $slug = strtolower(str_replace(' ', '.', $name));
                $company = Transfer::query()->create([
                    'name' => self::PREFIX . $name,
                    'address_first' => $address,
                    'city' => $cityId,
                    'country' => $country,
                    'work_phone' => $phone,
                    'work_email' => "ops@{$slug}.example.com",
                    'contact_name' => $contact,
                    'contact_phone' => $phone,
                    'comments' => 'Sample bus company for testing the Bus Company pages.',
                ]);
                $made['companies']++;

                $fleet[$name] = ['company' => $company, 'buses' => [], 'drivers' => []];
                foreach ($buses as $busName => $plate) {
                    $fleet[$name]['buses'][] = Bus::query()->create(['name' => self::PREFIX . $busName, 'bus_number' => $plate, 'transfer_id' => $company->id]);
                    $made['buses']++;
                }
                foreach ($drivers as $driverName => $driverPhone) {
                    $fleet[$name]['drivers'][] = Driver::query()->create([
                        'name' => self::PREFIX . $driverName,
                        'phone' => $driverPhone,
                        'email' => strtolower(str_replace(' ', '.', $driverName)) . "@{$slug}.example.com",
                        'transfer_id' => $company->id,
                    ]);
                    $made['drivers']++;
                }
            }

            $authorId = DB::table('users')->orderBy('id')->value('id');
            foreach (self::TRIPS as [$tripName, $companyName, $busIndex, $driverIndexes, $offset, $days, $statusName, $comment]) {
                $group = $fleet[$companyName];
                $bus = $group['buses'][$busIndex];
                $driverIds = array_map(function ($i) use ($group) {
                    return $group['drivers'][$i]->id;
                }, $driverIndexes);
                [$country, $cityName] = self::COMPANIES[$companyName];
                $cityId = DB::table('cities')->where('country', $country)->where('name', $cityName)->value('id');

                $trip = Trip::query()->create(['name' => self::PREFIX . $tripName]);
                $made['trips']++;
                $firstDay = null;
                for ($d = 0; $d < $days; $d++) {
                    $busDayHelper->createBusDayTrip(null, null, null, $today->copy()->addDays($offset + $d)->toDateString(),
                        $statuses[$statusName] ?? $statuses->first(), $bus->id, self::PREFIX . $tripName, $cityId, $country, $driverIds, $trip->id);
                    $made['trip days']++;
                    $firstDay = $firstDay ?: BusDay::query()->where('trip_id', $trip->id)->orderBy('date')->first();
                }

                if ($comment && $firstDay && $authorId) {
                    Comment::query()->create([
                        'content' => '<p>' . e($comment) . '</p>',
                        'author_id' => $authorId,
                        'reference_type' => Comment::$services['BusDay'],
                        'reference_id' => $firstDay->id,
                        'status' => 0,
                    ]);
                    $made['comments']++;
                }
            }

            return [$made, $fleet];
        });

        [$counts, $fleet] = $made;
        $counts['tour transfers'] = $this->option('standalone') ? 0 : $this->addTourTransfers($fleet, $today, $busDayHelper);

        $this->table(array_keys($counts), [array_values($counts)]);
        $this->info('Open Bus Company > Buses Calendar to see the trips. Remove them with: php artisan bus:sample-data --remove');

        return 0;
    }

    /** Adds a sample bus company to up to two upcoming real tours, as the calendar's "With tour" form does. */
    private function addTourTransfers(array $fleet, Carbon $today, BusDayHelper $busDayHelper)
    {
        $tours = DB::table('tours')->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('is_quotation')->orWhere('is_quotation', 0);
            })
            ->whereBetween('departure_date', [$today->toDateString(), $today->copy()->addDays(60)->toDateString()])
            ->whereNotNull('retirement_date')
            ->orderBy('departure_date')->limit(2)->get(['id', 'departure_date', 'retirement_date']);
        if ($tours->isEmpty()) {
            return 0;
        }

        $userId = DB::table('users')->orderBy('id')->value('id');
        Auth::loginUsingId($userId);

        $added = 0;
        $groups = array_values($fleet);
        foreach ($tours as $i => $tour) {
            $start = Carbon::parse($tour->departure_date)->toDateString();
            $end = Carbon::parse(min($tour->retirement_date, Carbon::parse($tour->departure_date)->addDays(2)->toDateString()))->toDateString();

            // first sample bus that is free on those days
            foreach ([$groups[($i + 2) % 3], $groups[$i % 3], $groups[($i + 1) % 3]] as $group) {
                foreach ($group['buses'] as $bus) {
                    if (!$busDayHelper->validateBuses($bus->id, $start, $end)['check']) {
                        continue;
                    }
                    $request = Request::create('/tour_package', 'POST', [
                        'tourId' => $tour->id,
                        'serviceType' => 'transfer',
                        'serviceId' => $group['company']->id,
                        'serviceName' => $group['company']->name,
                        'bus_id' => $bus->id,
                        'drivers_id' => [$group['drivers'][0]->id],
                        'dep_date_transfer' => $start,
                        'ret_date_transfer' => $end,
                    ]);
                    app()->instance('request', $request);
                    app(TourPackageController::class)->store($request);
                    $added++;
                    continue 3;
                }
            }
        }

        return $added;
    }

    private function remove()
    {
        $companyIds = Transfer::withTrashed()->where('name', 'like', self::PREFIX . '%')->pluck('id');
        $busIds = Bus::withTrashed()->where('name', 'like', self::PREFIX . '%')->pluck('id');
        $driverIds = Driver::query()->where('name', 'like', self::PREFIX . '%')->pluck('id');

        $userId = DB::table('users')->orderBy('id')->value('id');
        Auth::loginUsingId($userId);

        $counts = ['tour transfers' => 0];
        // tour transfers through the app's own delete (also removes their bus days and drivers)
        foreach (TourPackage::query()->where('type', 3)->whereIn('reference', $companyIds)->pluck('id') as $packageId) {
            app(TourPackageController::class)->destroy($packageId);
            $counts['tour transfers']++;
        }

        $counts += DB::transaction(function () use ($companyIds, $busIds, $driverIds) {
            $dayIds = BusDay::query()
                ->where(function ($q) use ($busIds) {
                    $q->whereIn('bus_id', $busIds)->orWhere('name_trip', 'like', self::PREFIX . '%');
                })->pluck('id');
            $tripIds = Trip::query()->where('name', 'like', self::PREFIX . '%')->pluck('id');

            return [
                'comments' => Comment::query()->where('reference_type', Comment::$services['BusDay'])->whereIn('reference_id', $dayIds)->delete(),
                'trip drivers' => TripToDrivers::query()->whereIn('bus_day_id', $dayIds)->delete(),
                'trip days' => BusDay::query()->whereIn('id', $dayIds)->delete(),
                'trips' => Trip::query()->whereIn('id', $tripIds)->delete(),
                'drivers' => Driver::query()->whereIn('id', $driverIds)->delete(),
                'buses' => Bus::withTrashed()->whereIn('id', $busIds)->forceDelete(),
                'companies' => Transfer::withTrashed()->whereIn('id', $companyIds)->forceDelete(),
            ];
        });

        $this->table(array_keys($counts), [array_values($counts)]);

        return 0;
    }
}
