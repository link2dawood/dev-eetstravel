<?php

namespace App\Console\Commands;

use App\Tour;
use App\TourDay;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Repairs tour days damaged by the old TourController@show bug, which rebuilt the
 * days from empty request dates on every page view: Day 1 was moved to "today"
 * and every other day was soft-deleted.
 *
 * For each tour date that has no active day it restores the soft-deleted day for
 * that date, or moves an out-of-range active day onto it. Nothing is deleted or
 * created. Dry run unless --apply is given.
 */
class RepairTourDays extends Command
{
    protected $signature = 'tours:repair-days {--tour=* : Only these tour ids} {--apply : Write the changes (default is a dry run)}';

    protected $description = 'Restore tour days that were collapsed to a single "today" day';

    public function handle()
    {
        $apply = (bool) $this->option('apply');
        $this->info($apply ? 'APPLY mode: changes will be written.' : 'DRY RUN: no changes will be written (use --apply).');

        $tours = Tour::query()->whereNotNull('departure_date')->whereNotNull('retirement_date')->orderBy('id');
        if ($this->option('tour')) {
            $tours->whereIn('id', $this->option('tour'));
        }

        $packageCounts = DB::table('packages_tour_days')
            ->select('tour_day_id', DB::raw('count(*) as c'))
            ->groupBy('tour_day_id')
            ->pluck('c', 'tour_day_id');

        $changed = 0;
        $flagged = 0;

        foreach ($tours->get(['id', 'name', 'departure_date', 'retirement_date']) as $tour) {
            $start = Carbon::parse($tour->departure_date)->startOfDay();
            $end = Carbon::parse($tour->retirement_date)->startOfDay();
            if ($end->lt($start) || $start->diffInDays($end) > 60) {
                $this->warn("Tour {$tour->id}: skipped, unusual date range {$start->toDateString()} .. {$end->toDateString()}");
                $flagged++;
                continue;
            }

            $expected = collect(CarbonPeriod::create($start, $end))->map->toDateString()->values();
            $days = TourDay::withTrashed()->where('tour', $tour->id)->orderBy('id')->get();
            $dateOf = function ($day) { return substr((string) $day->date, 0, 10); };

            $active = $days->filter(function ($day) { return $day->deleted_at === null; });
            $activeDates = $active->map($dateOf)->unique();
            $missing = $expected->diff($activeDates)->values();
            $outside = $active->filter(function ($day) use ($expected, $dateOf) {
                return !$expected->contains($dateOf($day));
            })->values();

            if ($missing->isEmpty() && $outside->isEmpty()) {
                continue;
            }

            $plan = [];
            foreach ($missing as $date) {
                $candidate = $days
                    ->filter(function ($day) use ($date, $dateOf) { return $day->deleted_at !== null && $dateOf($day) === $date; })
                    ->sortByDesc(function ($day) use ($packageCounts) { return [($packageCounts[$day->id] ?? 0), $day->id]; })
                    ->first();

                if ($candidate) {
                    $plan[] = ['restore', $candidate, $date];
                } elseif ($outside->isNotEmpty()) {
                    $plan[] = ['move', $outside->shift(), $date];
                } else {
                    $plan[] = ['missing', null, $date];
                }
            }

            $this->line('');
            $this->line("Tour {$tour->id} \"{$tour->name}\" ({$start->toDateString()} .. {$end->toDateString()})");
            foreach ($plan as [$action, $day, $date]) {
                $pk = $day ? ($packageCounts[$day->id] ?? 0) : 0;
                if ($action === 'restore') {
                    $this->line("  restore day #{$day->id} for {$date} ({$pk} services)");
                } elseif ($action === 'move') {
                    $this->line("  move day #{$day->id} from {$dateOf($day)} to {$date} ({$pk} services)");
                } else {
                    $this->warn("  no day record exists for {$date} - needs manual check");
                    $flagged++;
                }
            }
            foreach ($outside as $day) {
                $this->warn("  day #{$day->id} ({$dateOf($day)}, " . ($packageCounts[$day->id] ?? 0) . " services) is outside the tour range - left untouched");
                $flagged++;
            }

            if ($apply) {
                DB::transaction(function () use ($plan) {
                    foreach ($plan as [$action, $day, $date]) {
                        if ($action === 'restore') {
                            $day->restore();
                        } elseif ($action === 'move') {
                            $day->date = $date;
                            $day->save();
                        }
                    }
                });
            }
            $changed += collect($plan)->whereIn(0, ['restore', 'move'])->count();
        }

        $this->line('');
        $this->info(($apply ? 'Applied' : 'Would apply') . " {$changed} day change(s); {$flagged} item(s) need a manual look.");

        return 0;
    }
}
