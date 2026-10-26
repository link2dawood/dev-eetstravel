<?php

namespace App\Helper;

use App\City;
use App\Offices;
use App\Tour;
use App\TourDay;
use App\TourPackage;
use App\TourRoomTypeHotel;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Builds plain, display-ready data for the itinerary and voucher documents
 * (HTML, PDF and DOC), so every format reads the same way.
 *
 * Reads fresh from the database: the export controllers mutate package times
 * in place, which must not leak into these documents.
 */
class TourDocumentPresenter
{
    const TYPE_LABELS = [
        0 => 'Hotel', 1 => 'Sightseeing', 2 => 'Guide', 3 => 'Transfer',
        4 => 'Restaurant', 5 => 'Package', 6 => 'Cruise', 7 => 'Flight',
    ];

    /** @var Tour */
    protected $tour;

    public function __construct(Tour $tour)
    {
        $this->tour = $tour;
    }

    // ------------------------------------------------------------------ shared

    public function office()
    {
        $office = Offices::where('status', 1)->first();
        if (!$office) {
            return null;
        }
        return [
            'name' => $office->office_name,
            'address' => $office->office_address,
            'phone' => ltrim(trim((string) $office->tel), ' :'),
            'fax' => ltrim(trim((string) $office->fax), ' :'),
        ];
    }

    public function summary()
    {
        $tour = $this->tour;
        $start = $tour->departure_date ? Carbon::parse($tour->departure_date) : null;
        $end = $tour->retirement_date ? Carbon::parse($tour->retirement_date) : null;
        $days = ($start && $end) ? $start->diffInDays($end) + 1 : null;

        $responsible = User::find($tour->responsible);
        $children = $tour->childrens ?? collect();

        return [
            'name' => $tour->name,
            'code' => $tour->external_name,
            'start' => $start ? $start->format('D j M Y') : '-',
            'end' => $end ? $end->format('D j M Y') : '-',
            'duration' => $days ? $days . ' ' . ($days == 1 ? 'day' : 'days') . ' / ' . max($days - 1, 0) . ' ' . ($days - 1 == 1 ? 'night' : 'nights') : '',
            'route' => $this->route(),
            'group' => $this->groupText($children),
            'rooms' => $this->roomsText(TourRoomTypeHotel::where('tour_id', $tour->id)->get()),
            'tour_leader' => trim((string) $tour->itinerary_tl),
            'tour_leader_phone' => trim(strip_tags((string) $tour->phone)),
            'in_charge' => $responsible ? $responsible->name : '',
            'in_charge_email' => $responsible ? $responsible->email : '',
        ];
    }

    protected function route()
    {
        $from = trim($this->tour->city_begin . ($this->tour->country_begin ? ', ' . $this->tour->country_begin : ''), ', ');
        $to = trim($this->tour->city_end . ($this->tour->country_end ? ', ' . $this->tour->country_end : ''), ', ');
        if ($from === '' && $to === '') {
            return '';
        }
        return $from === $to ? $from : trim($from . ' → ' . $to, ' →');
    }

    protected function groupText($children)
    {
        $pax = (int) $this->tour->pax;
        $free = (int) $this->tour->getRawOriginal('pax_free');
        $text = $pax . ' ' . ($pax == 1 ? 'passenger' : 'passengers');
        if ($free) {
            $text .= ' + ' . $free . ' free';
        }
        if (count($children)) {
            $ages = collect($children)->pluck('age')->filter(fn($a) => $a !== null && $a !== '')->implode(', ');
            $text .= ' + ' . count($children) . ' ' . (count($children) == 1 ? 'child' : 'children') . ($ages !== '' ? " (age $ages)" : '');
        }
        return $text;
    }

    /** "4 × Single, 10 × Double" */
    protected function roomsText($roomRows)
    {
        return collect($roomRows)
            ->filter(fn($r) => $r->room_types && $r->count)
            ->map(fn($r) => $r->count . ' × ' . $r->room_types->name)
            ->implode(', ');
    }

    protected function serviceContact($package)
    {
        $service = $package->service();
        if (!$service) {
            return ['address' => '', 'phone' => '', 'email' => ''];
        }
        // Supplier city ids are unreliable (many point at unrelated cities), so use the address text only
        $address = collect([$service->address_first ?? '', $service->address_second ?? ''])
            ->map(fn($p) => trim(strip_tags((string) $p), " ,"))
            ->filter()->unique()->implode(', ');

        return [
            'address' => $address,
            'phone' => ltrim(trim((string) ($service->work_phone ?? '')), ' -/.,'),
            'email' => trim((string) ($service->work_email ?? '')),
        ];
    }

    protected function menusText($package)
    {
        return collect($package->menus)
            ->map(fn($m) => trim($m->count . ' × ' . optional($m->menu)->name))
            ->filter(fn($s) => $s !== '' && substr($s, -3) !== '× ')
            ->implode(', ');
    }

    /**
     * Editor HTML -> readable plain text: decode entities (&nbsp;, &ndash;, &#39;),
     * trim every line and drop blank lines.
     */
    protected function plainText($html)
    {
        $text = preg_replace('#<\s*(br|/p|/div|/li|/h[1-6])\s*/?\s*>#i', "\n", (string) $html);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\u{00A0}", ' ', $text);
        $lines = array_map(fn($l) => trim(preg_replace('/[ \t]+/', ' ', $l)), preg_split('/\R/', $text));
        // editors wrap every line in its own <p>, so blank lines carry no meaning: keep it compact
        $text = preg_replace("/\n{2,}/", "\n", implode("\n", $lines));
        return trim($text);
    }

    protected function time($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : '';
    }

    protected function packageDays()
    {
        return TourDay::where('tour', $this->tour->id)->orderBy('date')->get();
    }

    // --------------------------------------------------------------- itinerary

    /**
     * Day-by-day programme. Hotel stays appear once (check-in day) and as
     * "Overnight" on the following nights; descriptions become plain notes.
     */
    public function days(array $exclude = [])
    {
        $result = [];
        $openStays = [];   // parent hotel package id => ['name', 'until']

        foreach ($this->packageDays() as $i => $day) {
            $date = Carbon::parse($day->date);
            $items = [];
            $notes = [];
            $overnight = null;

            $packages = $day->packages
                ->reject(fn($p) => in_array($p->id, $exclude))
                ->sortBy(fn($p) => Carbon::parse($p->time_from)->format('H:i:s'));

            foreach ($packages as $p) {
                if ($p->description_package) {
                    if (($text = $this->plainText($p->description)) !== '') {
                        $notes[] = $text;
                    }
                    continue;
                }

                if ((int) $p->type === 0) {
                    $parentId = $p->parent_id ?: $p->id;
                    $root = $p->parent_id ? TourPackage::find($p->parent_id) : $p;
                    $checkOut = $root && $root->time_to ? Carbon::parse($root->time_to) : null;
                    $overnight = $p->name;
                    if ($p->parent_id) {
                        continue; // later nights of a stay: shown as "Overnight" only
                    }
                    $nights = $checkOut ? max($date->copy()->startOfDay()->diffInDays($checkOut->copy()->startOfDay()), 1) : 1;
                    $contact = $this->serviceContact($p);
                    $items[] = [
                        'time' => $this->time($p->time_from),
                        'type' => 'Hotel',
                        'title' => $p->name,
                        'lines' => array_filter([
                            'Check-in ' . $this->time($p->time_from) . ($checkOut ? ' · check-out ' . $checkOut->format('D j M') : '') . ' · ' . $nights . ' ' . ($nights == 1 ? 'night' : 'nights'),
                            $contact['address'] ? 'Address: ' . $contact['address'] : '',
                            $contact['phone'] ? 'Phone: ' . $contact['phone'] : '',
                            ($r = $this->roomsText($p->room_types_hotel)) ? 'Rooms: ' . $r : '',
                            ($m = $this->menusText($p)) ? 'Meals: ' . $m : '',
                            trim((string) $p->note) !== '' ? 'Note: ' . trim($p->note) : '',
                        ]),
                    ];
                    continue;
                }

                $contact = $this->serviceContact($p);
                $from = $this->time($p->time_from);
                $to = $this->time($p->time_to);
                $items[] = [
                    'time' => $from . ($to && $to !== $from ? '–' . $to : ''),
                    'type' => self::TYPE_LABELS[(int) $p->type] ?? 'Service',
                    'title' => $p->name,
                    'lines' => array_filter([
                        $contact['address'] ? 'Address: ' . $contact['address'] : '',
                        $contact['phone'] ? 'Phone: ' . $contact['phone'] : '',
                        ($m = $this->menusText($p)) ? 'Menu: ' . $m : '',
                        trim((string) $p->note) !== '' ? 'Note: ' . trim($p->note) : '',
                    ]),
                ];
            }

            // transfers are linked to the tour, not to a day: place them by date
            foreach ($this->transfers($exclude) as $t) {
                if ($t['date_key'] === $date->toDateString()) {
                    $items[] = [
                        'time' => $t['time'],
                        'type' => 'Transfer',
                        'title' => $t['title'],
                        'lines' => array_filter([$t['route'] ? 'Route: ' . $t['route'] : '', $t['vehicle'], $t['drivers']]),
                    ];
                }
            }
            usort($items, fn($a, $b) => strcmp($a['time'], $b['time']));

            $result[] = [
                'number' => $i + 1,
                'date' => $date->format('l, j F Y'),
                'items' => $items,
                'notes' => $notes,
                'overnight' => $overnight,
            ];
        }

        return $result;
    }

    public function hotels(array $exclude = [])
    {
        $stays = [];
        foreach ($this->packageDays() as $day) {
            foreach ($day->packages as $p) {
                if ((int) $p->type !== 0 || $p->parent_id || $p->description_package || in_array($p->id, $exclude)) {
                    continue;
                }
                $in = Carbon::parse($p->time_from);
                $out = $p->time_to ? Carbon::parse($p->time_to) : null;
                $nights = $out ? max($in->copy()->startOfDay()->diffInDays($out->copy()->startOfDay()), 1) : 1;
                $contact = $this->serviceContact($p);
                $stays[$p->id] = [
                    'name' => $p->name,
                    'check_in' => $in->format('D j M Y'),
                    'check_out' => $out ? $out->format('D j M Y') : '-',
                    'nights' => $nights,
                    'address' => $contact['address'],
                    'phone' => $contact['phone'],
                ];
            }
        }
        return array_values($stays);
    }

    public function transfers(array $exclude = [], $voucherOnly = false)
    {
        return TourPackage::where('tour_id', $this->tour->id)->where('type', 3)
            ->orderBy('time_from')->get()
            ->reject(fn($p) => in_array($p->id, $exclude) || ($voucherOnly && isset($p->vch) && (int) $p->vch === 0))
            ->map(function ($p) {
                $from = $p->time_from ? Carbon::parse($p->time_from) : null;
                $to = $p->time_to ? Carbon::parse($p->time_to) : null;
                $drivers = $p->getTransferDrivers();
                $busDay = \App\BusDay::where('tour_package_id', $p->id)->first();
                $bus = $busDay && $busDay->bus_id ? \App\Bus::find($busDay->bus_id) : null;
                return [
                    'package' => $p,
                    'date_key' => $from ? $from->toDateString() : '',
                    'date' => $from ? $from->format('D j M Y') : '-',
                    'time' => $from ? $from->format('H:i') . ($to && $to->format('H:i') !== $from->format('H:i') && $to->isSameDay($from) ? '–' . $to->format('H:i') : '') : '',
                    'title' => $p->name,
                    'route' => trim(($p->pickup_des ?: '') . ($p->pickup_des && $p->drop_des ? ' → ' : '') . ($p->drop_des ?: '')),
                    'vehicle' => $bus ? 'Coach: ' . $bus->name : '',
                    'drivers' => $drivers->count() ? 'Driver: ' . $drivers->map(fn($d) => trim($d->name . ($d->phone ? ' (' . $d->phone . ')' : '')))->implode(', ') : '',
                ];
            })->values()->all();
    }

    // ---------------------------------------------------------------- vouchers

    /**
     * One voucher per bookable service: each hotel stay (not every night),
     * every day service and every transfer. Respects the per-service vch flag.
     */
    public function vouchers(array $exclude = [])
    {
        $summary = $this->summary();
        $vouchers = [];

        foreach ($this->packageDays() as $day) {
            foreach ($day->packages->sortBy(fn($p) => Carbon::parse($p->time_from)->format('H:i:s')) as $p) {
                if ($p->description_package || $p->parent_id || in_array($p->id, $exclude) || (isset($p->vch) && (int) $p->vch === 0)) {
                    continue;
                }
                $vouchers[$p->id] = $this->voucherFor($p, $summary);
            }
        }
        foreach ($this->transfers($exclude, true) as $t) {
            $vouchers[$t['package']->id] = $this->voucherFor($t['package'], $summary, $t);
        }

        uasort($vouchers, fn($a, $b) => strcmp($a['sort'], $b['sort']));
        return array_values($vouchers);
    }

    protected function voucherFor(TourPackage $p, array $summary, array $transfer = null)
    {
        $type = (int) $p->type;
        $from = $p->time_from ? Carbon::parse($p->time_from) : null;
        $to = $p->time_to ? Carbon::parse($p->time_to) : null;
        $contact = $this->serviceContact($p);

        if ($type === 0) {
            $nights = ($from && $to) ? max($from->copy()->startOfDay()->diffInDays($to->copy()->startOfDay()), 1) : 1;
            $when = [
                'Check-in' => $from ? $from->format('D j M Y, H:i') : '-',
                'Check-out' => $to ? $to->format('D j M Y') : '-',
                'Nights' => $nights,
            ];
        } elseif ($from && $to && !$to->isSameDay($from)) {
            $when = ['From' => $from->format('D j M Y, H:i'), 'Until' => $to->format('D j M Y, H:i')];
        } else {
            $when = ['Date' => $from ? $from->format('D j M Y') : '-', 'Time' => $from ? $from->format('H:i') . ($to && $to->format('H:i') !== $from->format('H:i') ? '–' . $to->format('H:i') : '') : '-'];
        }

        $details = array_filter([
            'Rooms' => $this->roomsText($p->room_types_hotel),
            'Meals' => $this->menusText($p),
            'Route' => $transfer['route'] ?? '',
            'Vehicle' => isset($transfer['vehicle']) ? preg_replace('/^Coach: /', '', $transfer['vehicle']) : '',
            'Driver' => isset($transfer['drivers']) ? preg_replace('/^Driver: /', '', $transfer['drivers']) : '',
            'Notes' => trim((string) $p->note),
        ], fn($v) => $v !== '' && $v !== null);

        return [
            'sort' => ($from ? $from->format('Y-m-d H:i') : '9999') . '-' . $p->id,
            'number' => 'V-' . $this->tour->id . '-' . $p->id,
            'type' => self::TYPE_LABELS[$type] ?? 'Service',
            'supplier' => $p->name,
            'supplier_address' => $contact['address'],
            'supplier_phone' => $contact['phone'],
            'supplier_email' => $contact['email'],
            'when' => $when,
            'guests' => $summary['group'],
            'details' => $details,
            'tour' => $summary['name'] . ($summary['code'] ? ' (' . $summary['code'] . ')' : ''),
            'tour_leader' => trim($summary['tour_leader'] . ($summary['tour_leader_phone'] ? ' · ' . $summary['tour_leader_phone'] : ''), ' ·'),
            'in_charge' => $summary['in_charge'],
        ];
    }
}
