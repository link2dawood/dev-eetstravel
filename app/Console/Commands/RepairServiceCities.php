<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Repairs the city of service suppliers (hotels, events, restaurants, guides,
 * transfers). An old import stored city ids from a city list that no longer
 * exists, so most suppliers point to a city in another country (a hotel in Milan
 * shows "Longonjo") or to no city at all. Some restaurants hold a city name as
 * text, which the pages cannot show because they join on the city id.
 *
 * Only records whose city is missing or invalid for their country are touched.
 * The city is taken from the record itself: its free-text city, else a city named
 * in the supplier's name ("Mercure Hotel Hamburg City"), matched against the cities
 * of the record's own country. Addresses, websites and records sharing an old id
 * were tried and gave too many wrong cities, so they are not used.
 * A numeric city id that is still invalid after that is cleared, so the pages
 * show no city instead of a wrong one. Free-text city names are kept.
 *
 * Many imported rows also have the wrong country ("Hilton Dresden" under AT). These
 * are listed in the CSV; with --fix-country the country and city are taken from the
 * name when it names a city in exactly one of the countries the suppliers are in.
 * Country codes with stray spaces (" HR") are trimmed.
 *
 * Dry run unless --apply is given. Every change is written to a CSV under
 * storage/app/city-repair, which --restore=<file> puts back.
 */
class RepairServiceCities extends Command
{
    protected $signature = 'services:repair-cities
        {--table=* : Only these tables (hotels, events, restaurants, guides, transfers)}
        {--apply : Write the changes (default is a dry run)}
        {--fix-country : Also correct the country when the name names a city in another country}
        {--restore= : Undo an applied run from its CSV file}';

    protected $description = 'Fix supplier cities left pointing to the wrong country by an old import';

    const TABLES = ['hotels', 'events', 'restaurants', 'guides', 'transfers', 'clients'];

    // City names that are also common words in supplier names, addresses and email domains.
    const GENERIC = ['hof', 'orange', 'tours', 'mitte', 'centre', 'center', 'centrum', 'central', 'city', 'airport',
        'park', 'royal', 'grand', 'palace', 'golf', 'star', 'garden', 'beach', 'port', 'bay', 'castle', 'villa',
        'marina', 'plaza', 'europa', 'europe', 'ville', 'station', 'hotel', 'restaurant', 'gmbh', 'info', 'mail', 'best',
        'rivoli', 'lido', 'altstadt', 'forst', 'bergheim', 'croix', 'fontaine', 'vittoria', 'stella', 'portici',
        'la madeleine', 'madeleine', 'centar', 'centro', 'coburg', 'adler', 'alba', 'anna', 'antony', 'forest', 'gilly',
        'horn', 'jona', 'montana', 'most', 'roth', 'tara', 'westend', 'istra', 'tivoli', 'imperia', 'ragusa', 'lugo',
        'vladimir', 'orel', 'peterhof', 'schwarzenberg', 'lichtenberg', 'san lorenzo', 'san paolo', 'galanta', 'luce'];

    // A city name right after one of these is a street ("Via Milano", "Rue de Paris").
    const STREET_WORDS = ['via', 'viale', 'corso', 'piazza', 'piazzale', 'largo', 'vicolo', 'rue', 'de', 'du', 'des', 'avenue', 'av',
        'boulevard', 'bd', 'bld', 'place', 'quai', 'calle', 'avenida', 'paseo', 'plaza', 'rua', 'ulica', 'ul', 'ulice', 'utca',
        'str', 'strasse', 'gasse', 'allee', 'platz', 'weg', 'street', 'road', 'st', 'square', 'sq', 'nam', 'namesti', 'trg', 'ulitsa'];

    // Local or other spellings used in supplier data => the name in the city list.
    const ALIASES = [
        'DE' => ['munchen' => 'Munich', 'muenchen' => 'Munich', 'nuremberg' => 'Nürnberg', 'cologne' => 'Köln',
                 'hanover' => 'Hannover', 'frankfurt' => 'Frankfurt am Main', 'bad homburg' => 'Bad Homburg vor der Höhe'],
        'AT' => ['wien' => 'Vienna'],
        'CZ' => ['praha' => 'Prague', 'plzen' => 'Pilsen', 'karlsbad' => 'Karlovy Vary'],
        'IT' => ['roma' => 'Rome', 'milano' => 'Milan', 'firenze' => 'Florence', 'venezia' => 'Venice',
                 'napoli' => 'Naples', 'torino' => 'Turin', 'genova' => 'Genoa'],
        'PL' => ['warszawa' => 'Warsaw', 'krakau' => 'Kraków'],
        'PT' => ['lisboa' => 'Lisbon'],
        'BE' => ['bruxelles' => 'Brussels', 'brussel' => 'Brussels'],
        'DK' => ['kobenhavn' => 'Copenhagen'],
        'NL' => ['den haag' => 'The Hague'],
        'GR' => ['athina' => 'Athens'],
        'RS' => ['beograd' => 'Belgrade'],
        'RU' => ['moskva' => 'Moscow', 'st petersburg' => 'Saint Petersburg'],
        'CH' => ['lucerne' => 'Luzern'],
        'UA' => ['kiev' => 'Kyiv'],
    ];

    private $cities = [];
    private $index = [];
    private $anyCountry = [];

    public function handle()
    {
        if ($this->option('restore')) {
            return $this->restore($this->option('restore'));
        }

        $apply = (bool) $this->option('apply');
        $this->info($apply ? 'APPLY mode: changes will be written.' : 'DRY RUN: no changes will be written (use --apply).');

        $tables = $this->option('table') ?: self::TABLES;
        if ($unknown = array_diff($tables, self::TABLES)) {
            $this->error('Unknown table: ' . implode(', ', $unknown));
            return 1;
        }

        $this->loadCities();

        $dir = storage_path('app/city-repair');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $file = $dir . '/' . date('Ymd_His') . ($apply ? '_applied' : '_dry_run') . '.csv';
        $csv = fopen($file, 'w');
        $fixCountry = (bool) $this->option('fix-country');
        fputcsv($csv, ['table', 'id', 'name', 'country', 'old_city', 'old_city_name', 'new_city', 'new_city_name', 'source', 'changed', 'country_looks_wrong', 'new_country']);

        $rows = [];
        foreach ($tables as $table) {
            // " HR" never matches the country filters; trimming loses nothing
            $padded = DB::table($table)->whereRaw('country <> TRIM(country)')->count();
            if ($apply && $padded) {
                DB::table($table)->whereRaw('country <> TRIM(country)')->update(['country' => DB::raw('TRIM(country)')]);
            }

            $plan = $this->plan($table);
            $counts = ['already ok' => 0, 'found in record' => 0, 'cleared' => 0, 'unresolved' => 0];
            $wrongCountry = 0;

            foreach ($plan as $change) {
                $counts[$change['result']]++;
                $hint = empty($change['hint']) ? '' : 'name suggests ' . $this->cityLabel($change['hint']);
                $wrongCountry += $hint !== '';
                $newCountry = '';
                if ($hint !== '' && $fixCountry) {
                    $change['new'] = (string) $change['hint'];
                    $change['source'] = 'name, other country';
                    $newCountry = $this->cities[$change['hint']][1];
                }
                $changed = array_key_exists('new', $change);
                if (!$changed && $hint === '') {
                    continue;
                }
                fputcsv($csv, [
                    $table, $change['id'], $change['name'], $change['country'],
                    $change['old'], $this->cityLabel($change['old']),
                    $changed ? $change['new'] : '', $changed ? $this->cityLabel($change['new']) : '',
                    $change['source'] ?? '', $changed ? 'yes' : 'no', $hint, $newCountry,
                ]);
                if ($apply && $changed) {
                    DB::table($table)->where('id', $change['id'])
                        ->update($newCountry === '' ? ['city' => $change['new']] : ['city' => $change['new'], 'country' => $newCountry]);
                }
            }

            $rows[] = array_merge([$table], array_values($counts), [$wrongCountry . ($fixCountry ? ' (fixed)' : ''), $padded]);
        }

        fclose($csv);
        $this->table(['table', 'already ok', 'found in record', 'cleared', 'unresolved', 'country looks wrong', 'country trimmed'], $rows);
        $this->line('Details: ' . $file);
        if (!$fixCountry) {
            $this->line('"country looks wrong": the name points to a city in another country. Listed in the CSV; --fix-country corrects them.');
        }

        return 0;
    }

    private function plan($table)
    {
        $records = DB::table($table)->whereNull('deleted_at')
            ->get(array_values(array_filter(['id', 'name', 'address', 'address_first', 'address_second', 'city', 'country', 'website', 'work_email'], function ($column) use ($table) {
                return \Illuminate\Support\Facades\Schema::hasColumn($table, $column); // clients have "address", suppliers "address_first"
            })));

        // Countries this table really uses (at least 5 suppliers); keeps US/AU namesakes out of the hints
        $usedCountries = $records->map(function ($record) {
            return strtoupper(trim((string) $record->country));
        })->countBy()->filter(function ($n) {
            return $n >= 5;
        })->all();

        $plan = [];
        foreach ($records as $record) {
            $country = strtoupper(trim((string) $record->country));
            $old = trim((string) $record->city);
            $item = ['id' => $record->id, 'name' => trim((string) $record->name), 'country' => $country, 'old' => $old];

            if ($this->isValid($old, $country)) {
                $plan[] = $item + ['result' => 'already ok'];
                continue;
            }

            if ($match = $this->matchRecord($record, $country, $old)) {
                $plan[] = $item + ['result' => 'found in record', 'new' => (string) $match[0], 'source' => $match[1]];
                continue;
            }

            $item['hint'] = $this->otherCountryCity($record, $country, $old, $usedCountries);
            if (ctype_digit($old) && (int) $old > 0) {
                $plan[] = $item + ['result' => 'cleared', 'new' => null, 'source' => 'invalid id, no evidence'];
            } else {
                $plan[] = $item + ['result' => 'unresolved'];
            }
        }

        return $plan;
    }

    /**
     * A city in another country named by the record, when only one of the countries
     * the suppliers are in has a city of that name ("Hilton Dresden" filed under AT).
     * A name that is only "Hotel <city>" or names two cities is skipped: hotels are
     * often named after another city ("Hotel Roma" in Prague).
     */
    private function otherCountryCity($record, $country, $old, array $usedCountries)
    {
        $texts = [
            'city text' => ctype_digit($old) ? '' : $old,
            'name' => preg_replace('/\(?\s*arranged by.*$/i', '', (string) $record->name),
        ];
        foreach ($texts as $source => $text) {
            $words = array_values(array_filter(explode(' ', $this->key(Str::ascii((string) $text))), 'strlen'));
            $found = [];
            $rest = [];
            for ($i = 0, $count = count($words); $i < $count; $i++) {
                for ($len = min(4, $count - $i); $len >= 1; $len--) {
                    $key = implode(' ', array_slice($words, $i, $len));
                    if (strlen($key) >= 4 && isset($this->anyCountry[$key])) {
                        $found[$key] = true;
                        $i += $len - 1;
                        continue 2;
                    }
                }
                $rest[] = $words[$i];
            }
            if (count($found) !== 1) {
                if ($found) {
                    return null;
                }
                continue;
            }
            if ($source === 'name' && !array_diff($rest, ['hotel', 'hostel', 'pension', 'residence', 'inn', 'the', 'de', 'la', 'le', 'il', 'grand'])) {
                return null;
            }
            $candidates = array_intersect_key($this->anyCountry[key($found)], $usedCountries);
            unset($candidates[$country]);
            return count($candidates) === 1 ? reset($candidates) : null;
        }

        return null;
    }

    /** @return array|null [city id, source] */
    private function matchRecord($record, $country, $old)
    {
        // Address, website and email are not used: imported addresses are often wrong or name
        // a street after another city ("Via Roma"), and chains share mailboxes (reservations.berlin@...).
        $sources = [
            'city text' => ctype_digit($old) ? '' : $old,
            'name' => preg_replace('/\(?\s*arranged by.*$/i', '', (string) $record->name),
        ];
        foreach ($sources as $source => $text) {
            if ($id = $this->firstCityIn($text, $country)) {
                return [$id, $source];
            }
        }

        return null;
    }

    /** The first city of this country named in the text (longest name at that position). */
    private function firstCityIn($text, $country)
    {
        if (empty($this->index[$country]) || trim((string) $text) === '') {
            return null;
        }
        $words = explode(' ', $this->key(Str::ascii((string) $text)));
        $count = count($words);
        for ($i = 0; $i < $count; $i++) {
            for ($len = min(4, $count - $i); $len >= 1; $len--) {
                $key = implode(' ', array_slice($words, $i, $len));
                if (isset($this->index[$country][$key]) && !($i > 0 && in_array($words[$i - 1], self::STREET_WORDS, true))) {
                    return $this->index[$country][$key];
                }
            }
        }

        return null;
    }

    private function loadCities()
    {
        $ambiguous = [];
        $byName = [];
        foreach (DB::table('cities')->get(['id', 'name', 'country']) as $city) {
            $country = strtoupper(trim($city->country));
            $this->cities[$city->id] = [$city->name, $country];
            $byName[$country][$city->name] = $city->id;

            // "München" is indexed as "munchen" and "muenchen"
            $keys = array_unique([$this->key(Str::ascii($city->name)), $this->key(Str::ascii($city->name, 'de'))]);
            foreach ($keys as $key) {
                if (strlen($key) < 3 || in_array($key, self::GENERIC, true)) {
                    continue;
                }
                if (isset($this->index[$country][$key]) && $this->index[$country][$key] !== $city->id) {
                    $ambiguous[$country][$key] = true; // two towns with the same name: cannot tell which
                }
                $this->index[$country][$key] = $city->id;
            }
        }
        foreach ($ambiguous as $country => $keys) {
            foreach (array_keys($keys) as $key) {
                unset($this->index[$country][$key]);
            }
        }
        foreach (self::ALIASES as $country => $aliases) {
            foreach ($aliases as $alias => $name) {
                if (isset($byName[$country][$name])) {
                    $this->index[$country][$alias] = $byName[$country][$name];
                }
            }
        }
        foreach ($this->index as $country => $keys) {
            foreach ($keys as $key => $id) {
                $this->anyCountry[$key][$country] = $id;
            }
        }
    }

    private function key($text)
    {
        return trim(preg_replace('/[^a-z0-9]+/', ' ', strtolower((string) $text)));
    }

    private function isValid($city, $country)
    {
        return ctype_digit($city) && isset($this->cities[(int) $city]) && $this->cities[(int) $city][1] === $country;
    }

    private function cityLabel($city)
    {
        if ($city === null || $city === '') {
            return '';
        }
        return ctype_digit((string) $city) && isset($this->cities[(int) $city])
            ? $this->cities[(int) $city][0] . ' (' . $this->cities[(int) $city][1] . ')'
            : (ctype_digit((string) $city) ? 'unknown id' : $city);
    }

    private function restore($file)
    {
        if (!is_file($file)) {
            $file = storage_path('app/city-repair/' . $file);
        }
        if (!is_file($file) || !Str::contains($file, '_applied')) {
            $this->error('Give the CSV of an applied run (storage/app/city-repair/*_applied.csv).');
            return 1;
        }

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle);
        $restored = 0;
        while (($line = fgetcsv($handle)) !== false) {
            $row = array_combine($header, $line);
            if (!in_array($row['table'], self::TABLES, true)) {
                continue;
            }
            if (($row['changed'] ?? 'yes') !== 'yes') {
                continue;
            }
            $values = ['city' => $row['old_city'] === '' ? null : $row['old_city']];
            if (!empty($row['new_country'])) {
                $values['country'] = $row['country'];
            }
            DB::table($row['table'])->where('id', $row['id'])->update($values);
            $restored++;
        }
        fclose($handle);
        $this->info("Restored {$restored} records from {$file}");

        return 0;
    }
}
